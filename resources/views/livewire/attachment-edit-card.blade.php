@php
    $fileName = $attachmentData['file_name'] ?? '';
    $fileSize = $attachmentData['file_size'] ?? null;
    $url = $attachmentData['url'] ?? '';
    $captionValue = $caption !== '' ? $caption : ($attachmentData['caption'] ?? '');
@endphp

<div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-zinc-900">{{ $fileName }}</p>
            @if ($fileSize !== null)
                <p class="text-xs text-zinc-500">{{ number_format((int) $fileSize / 1024, 1) }} KB</p>
            @endif
        </div>

        @if ($showDelete)
            <button type="button" wire:click="confirmDelete" class="text-xs text-red-600">Delete</button>
        @endif
    </div>

    <div class="mt-3 flex gap-3">
        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600">Open</a>

        @if ($showPreview)
            <button type="button" wire:click="openPreview" class="text-sm text-zinc-600">Preview</button>
        @endif
    </div>

    @if ($editable)
        <div class="mt-3">
            <input
                type="text"
                wire:model.blur="caption"
                wire:blur="saveCaption"
                placeholder="Add a caption"
                class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"
            />

            @error('caption')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror

            @if ($captionValue)
                <button type="button" wire:click="confirmRemoveCaption" class="mt-2 text-xs text-zinc-500">
                    Remove caption
                </button>
            @endif
        </div>
    @elseif ($captionValue)
        <p class="mt-2 text-sm text-zinc-500">{{ $captionValue }}</p>
    @endif
</div>
