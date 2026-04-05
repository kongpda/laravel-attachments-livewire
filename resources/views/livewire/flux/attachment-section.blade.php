<div>
@if (count($attachments) > 0)
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($attachments as $attachment)
            <div wire:key="attachment-edit-{{ $attachment['id'] }}">
                @livewire(
                    \Kongpda\LaravelAttachments\Livewire\AttachmentEditCard::class,
                    [
                        'attachmentId' => $attachment['id'],
                        'attachmentData' => $attachment,
                        'editable' => $editable,
                        'showDelete' => $showDelete,
                        'showPreview' => $showPreview,
                    ]
                )
            </div>
        @endforeach
    </div>
@endif

<x-attachments.preview-modal
    modal-name="attachment-section-preview"
    :url="$previewUrl"
    :name="$previewName ?? ''"
    :type="$previewType ?? ''"
    wire-close-method="closePreview"
/>

<x-attachments.delete-confirmation-modal
    modal-name="attachment-section-delete"
    :file-name="$attachmentNameToDelete ?? ''"
    wire-click="performDelete"
    cancel-wire-click="cancelDeleteAttachment"
/>

<x-attachments.delete-confirmation-modal
    modal-name="attachment-section-remove-caption"
    :title="__('laravel-attachments::attachments.remove_caption')"
    :subtitle="__('laravel-attachments::attachments.remove_caption_confirm')"
    :warning-heading="__('laravel-attachments::attachments.remove_caption')"
    :warning-text="__('laravel-attachments::attachments.remove_caption_confirm')"
    wire-click="performRemoveCaption"
    cancel-wire-click="cancelRemoveCaption"
/>
</div>
