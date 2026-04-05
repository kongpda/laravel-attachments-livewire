<div class="space-y-4">
    @if (count($attachments) > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
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

    @if ($previewUrl)
        <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-zinc-900">{{ $previewName }}</p>
                    <p class="text-xs text-zinc-500">{{ $previewType }}</p>
                </div>

                <button type="button" wire:click="closePreview" class="text-sm text-zinc-600">Close</button>
            </div>

            <div class="mt-3">
                <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600">
                    Open preview
                </a>
            </div>
        </div>
    @endif

    @if ($attachmentToDelete)
        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-sm text-red-800">Delete {{ $attachmentNameToDelete }}?</p>
            <div class="mt-3 flex gap-3">
                <button type="button" wire:click="performDelete" class="text-sm text-red-700">Delete</button>
                <button type="button" wire:click="cancelDeleteAttachment" class="text-sm text-zinc-600">Cancel</button>
            </div>
        </div>
    @endif

    @if ($captionToRemoveFrom)
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm text-amber-800">Remove caption?</p>
            <div class="mt-3 flex gap-3">
                <button type="button" wire:click="performRemoveCaption" class="text-sm text-amber-700">Remove</button>
                <button type="button" wire:click="cancelRemoveCaption" class="text-sm text-zinc-600">Cancel</button>
            </div>
        </div>
    @endif
</div>
