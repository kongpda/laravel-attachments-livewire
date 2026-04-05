<?php

declare(strict_types=1);

namespace Kongpda\LaravelAttachments\Livewire;

use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Kongpda\LaravelAttachments\Support\AttachmentConfig;
use Livewire\Attributes\On;
use Livewire\Component;

class AttachmentSection extends Component
{
    public object $attachable;

    public array $attachments;

    public bool $editable = true;

    public bool $showDelete = true;

    public bool $showPreview = true;

    public string $policy = 'update';

    public ?string $previewUrl = null;

    public ?string $previewName = null;

    public ?string $previewType = null;

    public ?string $attachmentToDelete = null;

    public ?string $attachmentNameToDelete = null;

    public ?string $captionToRemoveFrom = null;

    public function mount(): void
    {
        $this->attachments ??= [];
    }

    #[On('caption-saved')]
    public function onCaptionSaved(?string $attachmentId = null, ?string $caption = null): void
    {
        if ($attachmentId !== null) {
            $index = collect($this->attachments)->search(fn (array $attachment): bool => ($attachment['id'] ?? '') === $attachmentId);

            if ($index !== false) {
                $this->attachments[$index]['caption'] = $caption;
            }
        }

        $this->toast(__('laravel-attachments::attachments.caption_saved'), 'success');
    }

    #[On('caption-save-failed')]
    public function onCaptionSaveFailed(): void
    {
        $this->toast(__('laravel-attachments::attachments.caption_save_failed'), 'danger');
    }

    #[On('preview-attachment')]
    public function previewAttachmentById(string $id): void
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

    #[On('confirm-delete-attachment')]
    public function confirmDeleteAttachment(string $id, string $fileName): void
    {
        $this->attachmentToDelete = $id;
        $this->attachmentNameToDelete = $fileName;

        $this->showModal('attachment-section-delete');
    }

    #[On('confirm-remove-caption')]
    public function confirmRemoveCaption(string $attachmentId): void
    {
        $this->captionToRemoveFrom = $attachmentId;

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

        $attachmentModel = AttachmentConfig::attachmentModel();
        $attachment = $attachmentModel::query()
            ->where('id', $this->captionToRemoveFrom)
            ->where('attachable_type', $this->attachable->getMorphClass())
            ->where('attachable_id', $this->attachable->getKey())
            ->first();

        if (! $attachment) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');
            $this->captionToRemoveFrom = null;
            $this->closeModal('attachment-section-remove-caption');

            return;
        }

        $this->authorize('update', $attachment);
        $attachment->update(['caption' => null]);

        $attachmentId = $this->captionToRemoveFrom;
        $index = collect($this->attachments)->search(fn (array $a): bool => ($a['id'] ?? '') === $attachmentId);

        if ($index !== false) {
            $this->attachments[$index]['caption'] = null;
        }

        $this->captionToRemoveFrom = null;
        $this->closeModal('attachment-section-remove-caption');
        $this->dispatch('caption-saved', attachmentId: $attachmentId, caption: null);
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

        $attachmentModel = AttachmentConfig::attachmentModel();
        $attachment = $attachmentModel::query()
            ->where('id', $this->attachmentToDelete)
            ->where('attachable_type', $this->attachable->getMorphClass())
            ->where('attachable_id', $this->attachable->getKey())
            ->first();

        if (! $attachment) {
            $this->toast(__('laravel-attachments::attachments.attachment_not_found'), 'danger');
            $this->cancelDeleteAttachment();

            return;
        }

        $this->authorize('delete', $attachment);
        $attachment->forceDeleteFromStorage();

        $this->cancelDeleteAttachment();
        $this->dispatch('attachment-deleted');
    }

    public function render(): Factory|View
    {
        $view = AttachmentConfig::usesFluxUi()
            ? 'laravel-attachments::livewire.flux.attachment-section'
            : 'laravel-attachments::livewire.attachment-section';

        return view($view);
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
