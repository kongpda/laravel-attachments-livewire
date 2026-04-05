<div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
    <div class="flex h-24 items-center justify-center overflow-hidden bg-zinc-50">
        @if ($previewUrl)
            @if ($isImage)
                <img src="{{ $previewUrl }}" alt="" class="h-full w-full object-contain" loading="lazy" />
            @else
                <iframe src="{{ $previewUrl }}#toolbar=0&navpanes=0" class="h-full w-full pointer-events-none" title="PDF preview" loading="lazy"></iframe>
            @endif
        @else
            <span class="text-xs text-zinc-500">{{ __('laravel-attachments::attachments.preview_unavailable') }}</span>
        @endif
    </div>

    <div class="space-y-3 p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900" title="{{ $file->getClientOriginalName() }}">{{ $file->getClientOriginalName() }}</p>
                <p class="text-xs text-zinc-500">{{ number_format($file->getSize() / 1024, 1) }} KB</p>
            </div>

            @if ($removeWireClick)
                <button type="button" wire:click="{{ $removeWireClick }}" class="text-xs text-red-600">
                    {{ __('laravel-attachments::attachments.remove') }}
                </button>
            @endif
        </div>

        @if ($captionWireModel)
            <input
                type="text"
                wire:model.blur="{{ $captionWireModel }}"
                placeholder="{{ $captionPlaceholder ?? __('laravel-attachments::attachments.attachment_caption_placeholder') }}"
                value="{{ $initialCaption }}"
                class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"
            />
        @endif
    </div>
</div>
