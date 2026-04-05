@php
    $hasCaption = ! empty($initialCaption);
@endphp

<flux:card class="flex flex-col overflow-hidden p-0">
    @if ($previewUrl && $previewWireClick)
        <button
            type="button"
            class="flex h-24 w-full shrink-0 items-center justify-center overflow-hidden bg-zinc-100 transition hover:bg-zinc-200 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-inset dark:bg-zinc-700/50 dark:hover:bg-zinc-600/50"
            wire:click="{{ $previewWireClick }}"
        >
            @if ($isImage)
                <img src="{{ $previewUrl }}" alt="" class="h-full w-full object-contain" loading="lazy" />
            @else
                <iframe src="{{ $previewUrl }}#toolbar=0&navpanes=0" class="h-full w-full pointer-events-none" title="PDF preview" loading="lazy"></iframe>
            @endif
        </button>
    @else
        <div class="flex h-24 shrink-0 items-center justify-center overflow-hidden bg-zinc-100 dark:bg-zinc-700/50">
            @if ($previewUrl)
                @if ($isImage)
                    <img src="{{ $previewUrl }}" alt="" class="h-full w-full object-contain" loading="lazy" />
                @else
                    <iframe src="{{ $previewUrl }}#toolbar=0&navpanes=0" class="h-full w-full pointer-events-none" title="PDF preview" loading="lazy"></iframe>
                @endif
            @else
                <flux:icon.document class="size-10 text-zinc-400" />
            @endif
        </div>
    @endif

    <div class="flex min-w-0 flex-1 flex-col p-4">
        <div class="flex items-start justify-between gap-2">
            <flux:text class="truncate text-sm font-medium" title="{{ $file->getClientOriginalName() }}">{{ $file->getClientOriginalName() }}</flux:text>
            @if ($removeWireClick)
                <flux:button
                    variant="ghost"
                    size="xs"
                    icon="x-mark"
                    class="shrink-0 text-zinc-500"
                    wire:click="{{ $removeWireClick }}"
                    title="{{ __('laravel-attachments::attachments.remove') }}"
                />
            @endif
        </div>

        <flux:text class="mt-0.5 text-xs text-zinc-500">{{ number_format($file->getSize() / 1024, 1) }} KB</flux:text>

        @if ($captionWireModel)
            <div class="mt-3" x-data="{ captionExpanded: @js($hasCaption) }">
                <button
                    x-show="!captionExpanded"
                    type="button"
                    @click="captionExpanded = true"
                    class="text-left text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300"
                >
                    {{ __('laravel-attachments::attachments.attachment_click_to_add_caption') }}
                </button>

                <div x-show="captionExpanded" x-cloak class="mt-0.5">
                    <flux:input
                        wire:model="{{ $captionWireModel }}"
                        :placeholder="$captionPlaceholder ?? __('laravel-attachments::attachments.attachment_caption_placeholder')"
                        size="sm"
                    />
                </div>
            </div>
        @endif
    </div>
</flux:card>
