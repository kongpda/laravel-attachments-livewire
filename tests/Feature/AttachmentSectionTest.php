<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Kongpda\LaravelAttachments\Http\Resources\AttachmentResource;
use Kongpda\LaravelAttachments\Livewire\AttachmentSection;
use Kongpda\LaravelAttachments\Models\Concerns\HasAttachments;
use Livewire\Livewire;

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
    });

    Storage::fake('local');
    // Guest-capable signature so the before-callback short-circuits for the
    // test's unauthenticated user (a zero-arg closure is skipped for guests).
    Gate::before(fn ($user = null): bool => true);
});

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
