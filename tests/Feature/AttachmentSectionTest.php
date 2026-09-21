<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Kongpda\LaravelAttachments\Http\Resources\AttachmentResource;
use Kongpda\LaravelAttachments\Livewire\AttachmentSection;
use Kongpda\LaravelAttachments\Models\Concerns\HasAttachments;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

/**
 * Minimal attachable used to exercise the consolidated section component.
 */
class SectionTestPost extends Model
{
    use HasAttachments;

    protected $table = 'section_test_posts';

    protected $guarded = [];

    public $timestamps = false;

    public function getMorphClass(): string
    {
        return 'section-test-post';
    }
}

beforeEach(function (): void {
    Schema::create('section_test_posts', function (Blueprint $table): void {
        $table->id();
        $table->string('title')->nullable();
        $table->unsignedBigInteger('user_id')->default(1);
    });

    Relation::morphMap(['section-test-post' => SectionTestPost::class]);
    Storage::fake('local');
    config()->set('attachments.storage.default_disk', 'local');

    // A real rule rather than a blanket allow, so a missing check fails a test.
    Gate::define('update', fn (User $user, SectionTestPost $post): bool => $post->user_id === $user->id);

    actingAs(sectionUser(1));
});

function sectionUser(int $id): User
{
    return (new User)->forceFill(['id' => $id]);
}

function sectionAttachment(SectionTestPost $post, array $overrides = []): object
{
    return $post->createAttachment(array_merge([
        'file_name' => 'doc.pdf',
        'file_path' => 'attachments/doc.pdf',
        'file_type' => 'application/pdf',
        'file_size' => 1024,
        'disk' => 'local',
    ], $overrides));
}

it('persists a caption to the owning attachment', function (): void {
    $post = SectionTestPost::create(['title' => 'Post']);
    $attachment = sectionAttachment($post);

    Livewire::test(AttachmentSection::class, [
        'attachable' => $post,
        'attachments' => [AttachmentResource::make($attachment)->resolve()],
    ])
        ->set("captions.{$attachment->id}", 'A nice caption')
        ->call('saveCaption', $attachment->id)
        ->assertHasNoErrors();

    expect($attachment->fresh()->caption)->toBe('A nice caption');
});

it('rejects a caption longer than 500 characters', function (): void {
    $post = SectionTestPost::create(['title' => 'Post']);
    $attachment = sectionAttachment($post);

    Livewire::test(AttachmentSection::class, [
        'attachable' => $post,
        'attachments' => [AttachmentResource::make($attachment)->resolve()],
    ])
        ->set("captions.{$attachment->id}", str_repeat('x', 501))
        ->call('saveCaption', $attachment->id)
        ->assertHasErrors("captions.{$attachment->id}");

    expect($attachment->fresh()->caption)->toBeNull();
});

it('does not update an attachment owned by a different attachable', function (): void {
    $postA = SectionTestPost::create(['title' => 'A']);
    $postB = SectionTestPost::create(['title' => 'B']);
    $attachmentB = sectionAttachment($postB, ['caption' => 'original']);

    // The section is mounted for post A but is fed post B's attachment data.
    Livewire::test(AttachmentSection::class, [
        'attachable' => $postA,
        'attachments' => [AttachmentResource::make($attachmentB)->resolve()],
    ])
        ->set("captions.{$attachmentB->id}", 'hijacked')
        ->call('saveCaption', $attachmentB->id);

    expect($attachmentB->fresh()->caption)->toBe('original');
});

it('removes a caption from the owning attachment', function (): void {
    $post = SectionTestPost::create(['title' => 'Post']);
    $attachment = sectionAttachment($post, ['caption' => 'remove me']);

    Livewire::test(AttachmentSection::class, [
        'attachable' => $post,
        'attachments' => [AttachmentResource::make($attachment)->resolve()],
    ])
        ->call('confirmRemoveCaption', $attachment->id)
        ->call('performRemoveCaption');

    expect($attachment->fresh()->caption)->toBeNull();
});

it('deletes an attachment and drops it from the list', function (): void {
    $post = SectionTestPost::create(['title' => 'Post']);
    $attachment = sectionAttachment($post);

    Livewire::test(AttachmentSection::class, [
        'attachable' => $post,
        'attachments' => [AttachmentResource::make($attachment)->resolve()],
    ])
        ->call('confirmDeleteAttachment', $attachment->id)
        ->call('performDelete')
        ->assertSet('attachments', []);

    expect($post->attachments()->count())->toBe(0);
});

it('uploads a file through UploadAttachment and appends it to the list', function (): void {
    $post = SectionTestPost::create(['title' => 'Post']);

    Livewire::test(AttachmentSection::class, [
        'attachable' => $post,
        'attachments' => [],
        'allowUpload' => true,
    ])
        ->set('files', [UploadedFile::fake()->create('notes.txt', 10, 'text/plain')])
        ->assertHasNoErrors();

    expect($post->attachments()->count())->toBe(1)
        ->and($post->attachments()->first()->file_name)->toBe('notes.txt');
});

it('does not upload when allowUpload is false', function (): void {
    $post = SectionTestPost::create(['title' => 'Post']);

    Livewire::test(AttachmentSection::class, [
        'attachable' => $post,
        'attachments' => [],
        'allowUpload' => false,
    ])
        ->set('files', [UploadedFile::fake()->create('notes.txt', 10, 'text/plain')]);

    expect($post->attachments()->count())->toBe(0);
});

it('refuses to let the browser change what the section is allowed to do', function (string $property, mixed $value): void {
    // These are set by the host's Blade at mount. If a request could set them,
    // a read-only section becomes an upload form by editing one payload field.
    $post = SectionTestPost::create(['title' => 'Post']);

    Livewire::test(AttachmentSection::class, ['attachable' => $post, 'attachments' => []])
        ->set($property, $value);
})->with([
    'allowUpload' => ['allowUpload', true],
    'policy' => ['policy', 'view'],
    'editable' => ['editable', true],
    'showDelete' => ['showDelete', true],
])->throws(CannotUpdateLockedPropertyException::class);

it('does not store an upload when the section was mounted without uploads', function (): void {
    $post = SectionTestPost::create(['title' => 'Post']);

    Livewire::test(AttachmentSection::class, ['attachable' => $post, 'attachments' => []])
        ->set('files', [UploadedFile::fake()->image('photo.png')]);

    expect($post->attachments()->count())->toBe(0);
});

it('refuses every change from a user who may not update the parent', function (): void {
    $post = SectionTestPost::create(['title' => 'Post', 'user_id' => 1]);
    $attachment = sectionAttachment($post, ['caption' => 'original']);

    actingAs(sectionUser(2));

    $section = fn () => Livewire::test(AttachmentSection::class, [
        'attachable' => $post,
        'attachments' => [AttachmentResource::make($attachment)->resolve()],
        'allowUpload' => true,
    ]);

    $section()->set("captions.{$attachment->id}", 'hijacked')->call('saveCaption', $attachment->id)->assertForbidden();
    $section()->call('confirmDeleteAttachment', $attachment->id)->call('performDelete')->assertForbidden();
    $section()->call('confirmRemoveCaption', $attachment->id)->call('performRemoveCaption')->assertForbidden();
    $section()->set('files', [UploadedFile::fake()->create('notes.txt', 10, 'text/plain')])->assertForbidden();

    expect($attachment->fresh()->caption)->toBe('original')
        ->and($post->attachments()->count())->toBe(1);
});
