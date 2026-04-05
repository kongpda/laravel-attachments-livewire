@php
    $thumbOrImageUrl = $isImage ? $url : ($thumbnailUrl ?? null);
@endphp

<div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm {{ $markedForRemoval ? 'opacity-40' : '' }}">
    <div class="flex h-24 items-center justify-center overflow-hidden bg-zinc-50">
        @if ($thumbOrImageUrl)
            <img src="{{ $thumbOrImageUrl }}" alt="" class="h-full w-full object-contain" loading="lazy" />
        @else
            <span class="text-xs text-zinc-500">{{ __('laravel-attachments::attachments.preview_unavailable') }}</span>
        @endif
    </div>

    <div class="space-y-3 p-4">
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-zinc-900" title="{{ $fileName }}">{{ $fileName }}</p>
            @if ($fileSize !== null && $fileSize !== '')
                <p class="text-xs text-zinc-500">{{ number_format((int) $fileSize / 1024, 1) }} KB</p>
            @endif
            @if ($resolvedCaption)
                <p class="mt-1 text-xs text-zinc-500">{{ $resolvedCaption }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 text-sm">
            @if ($isPreviewable && $showPreview && $previewWireClick)
                <button type="button" wire:click="{{ $previewWireClick }}" class="text-zinc-700">
                    {{ __('laravel-attachments::attachments.preview') }}
                </button>
            @endif

            @if ($showDownload && $url)
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600">
                    {{ __('laravel-attachments::attachments.download') }}
                </a>
            @endif

            @if ($showDelete && $deleteWireClick)
                <button type="button" wire:click="{{ $deleteWireClick }}" class="text-red-600">
                    {{ __('laravel-attachments::attachments.remove') }}
                </button>
            @endif
        </div>
    </div>
</div>
