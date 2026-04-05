<?php

declare(strict_types=1);

namespace Kongpda\LaravelAttachments\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Kongpda\LaravelAttachments\Support\AttachmentConfig;
use Livewire\Component;

class AttachmentEditCard extends Component
{
    public string $attachmentId;

    public array $attachmentData;

    public bool $editable = true;

    public bool $showDelete = true;

    public bool $showPreview = true;

    public string $caption = '';

    public bool $saved = false;

    public bool $expanded = false;

    public function mount(): void
    {
        $this->caption = (string) ($this->attachmentData['caption'] ?? $this->attachmentData['description'] ?? '');
        $this->expanded = $this->caption !== '';
    }

    public function updatedAttachmentData(): void
    {
        $this->caption = (string) ($this->attachmentData['caption'] ?? $this->attachmentData['description'] ?? '');
        $this->expanded = $this->caption !== '';
    }

    public function expandCaption(): void
    {
        $this->expanded = true;
    }

    public function saveCaption(): void
    {
        $validated = $this->validate([
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $newCaption = mb_trim((string) ($validated['caption'] ?? '')) ?: null;
        $currentCaption = mb_trim((string) ($this->attachmentData['caption'] ?? $this->attachmentData['description'] ?? '')) ?: null;

        if ($newCaption === $currentCaption) {
            $this->expanded = $newCaption !== null;

            return;
        }

        $attachmentModel = AttachmentConfig::attachmentModel();
        $attachment = $attachmentModel::query()->find($this->attachmentId);

        if (! $attachment) {
            $this->dispatch('caption-save-failed');

            return;
        }

        $this->authorize('update', $attachment);

        $attachment->update(['caption' => $newCaption]);

        $this->saved = ! in_array($newCaption, [null, '', '0'], true);
        $this->caption = (string) $newCaption;
        $this->expanded = $this->caption !== '';
        $this->attachmentData['caption'] = $newCaption;
        $this->dispatch('caption-saved', attachmentId: $this->attachmentId, caption: $newCaption);
    }

    public function openPreview(): void
    {
        $this->dispatch('preview-attachment', id: $this->attachmentId);
    }

    public function confirmRemoveCaption(): void
    {
        $this->dispatch('confirm-remove-caption', attachmentId: $this->attachmentId);
    }

    public function confirmDelete(): void
    {
        $this->dispatch('confirm-delete-attachment', id: $this->attachmentId, fileName: $this->attachmentData['file_name']);
    }

    public function render(): Factory|View
    {
        $view = AttachmentConfig::usesFluxUi()
            ? 'laravel-attachments::livewire.flux.attachment-edit-card'
            : 'laravel-attachments::livewire.attachment-edit-card';

        return view($view);
    }
}
