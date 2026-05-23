<?php

declare(strict_types=1);

namespace Kongpda\LaravelAttachments\Livewire;

use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Kongpda\LaravelAttachments\Actions\UploadAttachment;
use Kongpda\LaravelAttachments\Exceptions\DisallowedMimeException;
use Kongpda\LaravelAttachments\Exceptions\FileTooLargeException;
use Kongpda\LaravelAttachments\Http\Resources\AttachmentResource;
use Kongpda\LaravelAttachments\Support\AttachmentConfig;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class AttachmentSection extends Component
{
    use WithFileUploads;

    public object $attachable;

    /** @var array<int, array<string, mixed>> */
    public array $attachments;

    public bool $editable = true;

    public bool $showDelete = true;

    public bool $showPreview = true;

    public bool $allowUpload = false;

    public string $policy = 'update';

    /**
     * Pending file uploads bound to the upload dropzone.
     *
     * @var array<int, TemporaryUploadedFile>
     */
    public array $files = [];

    public ?string $uploadError = null;

    /**
     * Caption draft per attachment id (keyed map for the consolidated cards).
     *
     * @var array<string, string>
     */
    public array $captions = [];

    /**
     * Whether each attachment's caption editor is expanded (Flux UI only).
     *
     * @var array<string, bool>
     */
    public array $expandedCaptions = [];

    public ?string $recentlySavedId = null;

    public ?string $previewUrl = null;

    public ?string $previewName = null;

    public ?string $previewType = null;

    public ?string $attachmentToDelete = null;

    public ?string $attachmentNameToDelete = null;

    public ?string $captionToRemoveFrom = null;

    public function mount(): void
    {
        $this->attachments ??= [];

        foreach ($this->attachments as $attachment) {
            $id = $attachment['id'] ?? null;

            if ($id === null) {
                continue;
            }

            $caption = (string) ($attachment['caption'] ?? $attachment['description'] ?? '');
            $this->captions[$id] = $caption;
            $this->expandedCaptions[$id] = $caption !== '';
        }
    }

    public function updatedFiles(): void
    {
        if (! $this->allowUpload || $this->files === []) {
            return;
        }

        $attachable = $this->attachable;

        if (! $attachable instanceof Model) {
            return;
        }

        $this->authorize($this->policy, $attachable);
        $this->validate(['files.*' => ['file']]);

        $uploader = app(UploadAttachment::class);
        $this->uploadError = null;

        foreach ($this->files as $file) {
            try {
                $attachment = $uploader->handle($attachable, $file);
            } catch (FileTooLargeException|DisallowedMimeException $exception) {
                $this->uploadError = $exception->getMessage();
                $this->toast($exception->getMessage(), 'danger');

                continue;
            }

            /** @var array<string, mixed> $data */
            $data = AttachmentResource::make($attachment)->resolve();
            $id = (string) $data['id'];

            $this->attachments[] = $data;
            $this->captions[$id] = (string) ($data['caption'] ?? '');
            $this->expandedCaptions[$id] = $this->captions[$id] !== '';

            $this->dispatch('attachment-uploaded', id: $id);
        }

        $this->files = [];

        if ($this->uploadError === null) {
            $this->toast(__('laravel-attachments::attachments.upload_success'), 'success');
        }
    }

    public function expandCaption(string $id): void
    {
        $this->expandedCaptions[$id] = true;
    }

    public function saveCaption(string $id): void
    {
        $this->validate(['captions.'.$id => ['nullable', 'string', 'max:500']]);

        $index = collect($this->attachments)->search(fn (array $a): bool => ($a['id'] ?? '') === $id);

        if ($index === false) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');

            return;
        }

        $newCaption = mb_trim((string) ($this->captions[$id] ?? '')) ?: null;
        $currentCaption = mb_trim((string) ($this->attachments[$index]['caption'] ?? $this->attachments[$index]['description'] ?? '')) ?: null;

        if ($newCaption === $currentCaption) {
            $this->expandedCaptions[$id] = $newCaption !== null;

            return;
        }

        $attachment = $this->findOwnedAttachment($id);

        if (! $attachment) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');

            return;
        }

        $this->authorize('update', $attachment);
        $attachment->update(['caption' => $newCaption]);

        $this->attachments[$index]['caption'] = $newCaption;
        $this->captions[$id] = (string) $newCaption;
        $this->expandedCaptions[$id] = $this->captions[$id] !== '';
        $this->recentlySavedId = $newCaption !== null ? $id : null;

        $this->toast(__('laravel-attachments::attachments.caption_saved'), 'success');
        $this->dispatch('caption-saved', attachmentId: $id, caption: $newCaption);
    }

    public function openPreview(string $id): void
    {
        $attachment = collect($this->attachments)->firstWhere('id', $id);

        if (! $attachment) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');

            return;
        }

        $this->previewUrl = $attachment['url'];
        $this->previewName = $attachment['file_name'];
        $this->previewType = $attachment['file_type'] ?? '';

        $this->showModal('attachment-section-preview');
    }

    public function confirmDeleteAttachment(string $id): void
    {
        $attachment = collect($this->attachments)->firstWhere('id', $id);

        if (! $attachment) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');

            return;
        }

        $this->attachmentToDelete = $id;
        $this->attachmentNameToDelete = (string) ($attachment['file_name'] ?? '');

        $this->showModal('attachment-section-delete');
    }

    public function confirmRemoveCaption(string $id): void
    {
        $this->captionToRemoveFrom = $id;

        $this->showModal('attachment-section-remove-caption');
    }

    public function cancelDeleteAttachment(): void
    {
        $this->attachmentToDelete = null;
        $this->attachmentNameToDelete = null;

        $this->closeModal('attachment-section-delete');
    }

    public function cancelRemoveCaption(): void
    {
        $this->captionToRemoveFrom = null;

        $this->closeModal('attachment-section-remove-caption');
    }

    public function performRemoveCaption(): void
    {
        if (! $this->captionToRemoveFrom) {
            return;
        }

        $this->authorize($this->policy, $this->attachable);

        $id = $this->captionToRemoveFrom;
        $attachment = $this->findOwnedAttachment($id);

        if (! $attachment) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');
            $this->captionToRemoveFrom = null;
            $this->closeModal('attachment-section-remove-caption');

            return;
        }

        $this->authorize('update', $attachment);
        $attachment->update(['caption' => null]);

        $index = collect($this->attachments)->search(fn (array $a): bool => ($a['id'] ?? '') === $id);

        if ($index !== false) {
            $this->attachments[$index]['caption'] = null;
        }

        $this->captions[$id] = '';
        $this->expandedCaptions[$id] = false;
        $this->captionToRemoveFrom = null;
        $this->closeModal('attachment-section-remove-caption');
        $this->toast(__('laravel-attachments::attachments.caption_saved'), 'success');
        $this->dispatch('caption-saved', attachmentId: $id, caption: null);
    }

    public function closePreview(): void
    {
        $this->previewUrl = null;
        $this->previewName = null;
        $this->previewType = null;

        $this->closeModal('attachment-section-preview');
    }

    public function performDelete(): void
    {
        if (! $this->attachmentToDelete) {
            return;
        }

        $this->authorize($this->policy, $this->attachable);

        $id = $this->attachmentToDelete;
        $attachment = $this->findOwnedAttachment($id);

        if (! $attachment) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');
            $this->cancelDeleteAttachment();

            return;
        }

        $this->authorize('delete', $attachment);
        $attachment->forceDeleteFromStorage();

        $index = collect($this->attachments)->search(fn (array $a): bool => ($a['id'] ?? '') === $id);

        if ($index !== false) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }

        unset($this->captions[$id], $this->expandedCaptions[$id]);

        $this->cancelDeleteAttachment();
        $this->dispatch('attachment-deleted', id: $id);
    }

    public function render(): Factory|View
    {
        $view = AttachmentConfig::usesFluxUi()
            ? 'laravel-attachments::livewire.flux.attachment-section'
            : 'laravel-attachments::livewire.attachment-section';

        return view($view);
    }

    /**
     * Locate an attachment owned by this section's attachable, scoped by morph type + id.
     */
    private function findOwnedAttachment(string $id): ?object
    {
        $attachmentModel = AttachmentConfig::attachmentModel();

        return $attachmentModel::query()
            ->where('id', $id)
            ->where('attachable_type', $this->attachable->getMorphClass())
            ->where('attachable_id', $this->attachable->getKey())
            ->first();
    }

    private function toast(string $message, string $variant): void
    {
        if (AttachmentConfig::usesFluxUi() && class_exists(Flux::class)) {
            Flux::toast($message, variant: $variant);
        }
    }

    private function showModal(string $name): void
    {
        if (AttachmentConfig::usesFluxUi() && class_exists(Flux::class)) {
            Flux::modal($name)->show();
        }
    }

    private function closeModal(string $name): void
    {
        if (AttachmentConfig::usesFluxUi() && class_exists(Flux::class)) {
            Flux::modal($name)->close();
        }
    }
}
