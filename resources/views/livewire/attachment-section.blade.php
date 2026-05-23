<div class="space-y-4">
    @if ($allowUpload)
        <x-attachments.upload-section
            wire-model="files"
            :multiple="true"
            :error="$uploadError"
        />
    @endif

    @if (count($attachments) > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($attachments as $attachment)
                @php
                    $id = $attachment['id'];
                    $fileName = $attachment['file_name'] ?? '';
                    $fileSize = $attachment['file_size'] ?? null;
                    $url = $attachment['url'] ?? '';
                    $captionValue = $captions[$id] ?? ($attachment['caption'] ?? '');
                @endphp

                <div wire:key="attachment-card-{{ $id }}" class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900">{{ $fileName }}</p>
                            @if ($fileSize !== null)
                                <p class="text-xs text-zinc-500">{{ number_format((int) $fileSize / 1024, 1) }} KB</p>
                            @endif
                        </div>

                        @if ($showDelete)
                            <button type="button" wire:click="confirmDeleteAttachment('{{ $id }}')" class="text-xs text-red-600">Delete</button>
                        @endif
                    </div>

                    <div class="mt-3 flex gap-3">
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600">Open</a>

                        @if ($showPreview)
                            <button type="button" wire:click="openPreview('{{ $id }}')" class="text-sm text-zinc-600">Preview</button>
                        @endif
                    </div>

                    @if ($editable)
                        <div class="mt-3">
                            <input
                                type="text"
                                wire:model.blur="captions.{{ $id }}"
                                wire:blur="saveCaption('{{ $id }}')"
                                placeholder="{{ __('laravel-attachments::attachments.attachment_caption_placeholder') }}"
                                class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"
                            />

                            @error('captions.'.$id)
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            @if ($captionValue)
                                <button type="button" wire:click="confirmRemoveCaption('{{ $id }}')" class="mt-2 text-xs text-zinc-500">
                                    {{ __('laravel-attachments::attachments.remove_caption') }}
                                </button>
                            @endif
                        </div>
                    @elseif ($captionValue)
                        <p class="mt-2 text-sm text-zinc-500">{{ $captionValue }}</p>
                    @endif
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
