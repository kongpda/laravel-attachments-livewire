@php
    $thumbOrImageUrl = $isImage ? $url : ($thumbnailUrl ?? null);
@endphp

<flux:card class="group flex flex-col overflow-hidden p-0 {{ $markedForRemoval ? 'opacity-40' : '' }}">
    @if ($isPreviewable && $showPreview && $previewWireClick)
        <button
            type="button"
            class="flex h-24 w-full shrink-0 items-center justify-center overflow-hidden bg-zinc-100 transition hover:bg-zinc-200 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-inset dark:bg-zinc-700/50 dark:hover:bg-zinc-600/50"
            wire:click="{{ $previewWireClick }}"
        >
            @if ($thumbOrImageUrl)
                <span class="relative flex h-full w-full items-center justify-center">
                    <img
                        src="{{ $thumbOrImageUrl }}"
                        alt=""
                        class="h-full w-full object-contain"
                        loading="lazy"
                        onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');"
                    />
                    <flux:icon.document-text class="hidden size-10 text-zinc-400" />
                </span>
            @else
                <flux:icon.document-text class="size-10 text-zinc-400" />
            @endif
        </button>
    @else
        <a
            href="{{ $url }}"
            target="_blank"
            rel="noopener noreferrer"
            class="flex h-24 w-full shrink-0 items-center justify-center overflow-hidden bg-zinc-100 transition hover:bg-zinc-200 dark:bg-zinc-700/50 dark:hover:bg-zinc-600/50"
        >
            @if ($thumbOrImageUrl)
                <span class="relative flex h-full w-full items-center justify-center">
                    <img
                        src="{{ $thumbOrImageUrl }}"
                        alt=""
                        class="h-full w-full object-contain"
                        loading="lazy"
                        onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');"
                    />
                    <flux:icon.document class="hidden size-10 text-zinc-400" />
                </span>
            @else
                <flux:icon.document class="size-10 text-zinc-400" />
            @endif
        </a>
    @endif

    <div class="flex min-w-0 flex-1 flex-col p-4">
        <div class="flex items-start justify-between gap-2">
            <flux:text class="truncate text-sm font-medium" title="{{ $fileName }}">{{ $fileName }}</flux:text>
            @if ($markedForRemoval && $undoRemovalWireClick)
                <flux:button
                    variant="ghost"
                    size="xs"
                    icon="arrow-uturn-left"
                    class="shrink-0 text-blue-500"
                    wire:click="{{ $undoRemovalWireClick }}"
                    title="{{ __('laravel-attachments::attachments.cancel') }}"
                />
            @endif
        </div>

        @if ($fileSize !== null && $fileSize !== '')
            <flux:text class="mt-0.5 text-xs text-zinc-500">{{ number_format((int) $fileSize / 1024, 1) }} KB</flux:text>
        @endif

        @if ($resolvedCaption)
            <flux:text class="mt-1 line-clamp-2 text-xs text-zinc-500">{{ $resolvedCaption }}</flux:text>
        @endif

        @if (($showPreview || $showDownload) && ! $markedForRemoval)
            <div class="mt-3 flex items-center gap-1 border-t border-zinc-200 pt-3 dark:border-zinc-600">
                @if ($isPreviewable && $showPreview && $previewWireClick)
                    <flux:button
                        variant="ghost"
                        size="xs"
                        icon="eye"
                        class="text-zinc-500"
                        wire:click="{{ $previewWireClick }}"
                        title="{{ __('laravel-attachments::attachments.preview') }}"
                    />
                @endif

                @if ($showDownload && $url)
                    <a
                        href="{{ $url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded p-1.5 text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400"
                        title="{{ __('laravel-attachments::attachments.download') }}"
                    >
                        <flux:icon.arrow-down-tray class="size-4" />
                    </a>
                @endif

                @if ($showDelete && $deleteWireClick)
                    <flux:button
                        variant="ghost"
                        size="xs"
                        icon="trash"
                        class="text-zinc-500 hover:text-red-600"
                        wire:click="{{ $deleteWireClick }}"
                        title="{{ __('laravel-attachments::attachments.remove') }}"
                    />
                @endif
            </div>
        @endif
    </div>
</flux:card>
