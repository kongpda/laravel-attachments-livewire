<flux:modal :name="$modalName" class="max-w-4xl" variant="bare" aria-label="Attachment preview">
    <div class="overflow-hidden rounded-xl bg-white dark:bg-zinc-800" role="document">
        <div class="flex items-center justify-between border-b p-4 dark:border-zinc-700">
            <flux:heading size="lg" class="truncate pr-4">{{ $name }}</flux:heading>
            <div class="flex items-center gap-2">
                @if ($url)
                    <a
                        href="{{ $url }}"
                        target="_blank"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
                    >
                        <flux:icon.arrow-top-right-on-square class="size-4" />
                        {{ __('laravel-attachments::attachments.open') }}
                    </a>
                @endif
                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="{{ $wireCloseMethod }}" />
            </div>
        </div>
        <div class="max-h-[75vh] overflow-auto bg-zinc-100 p-4 dark:bg-zinc-900">
            @if ($url && $type)
                @if (str_starts_with($type, 'image/'))
                    <img src="{{ $url }}" alt="{{ $name }}" class="mx-auto h-auto max-w-full rounded-lg shadow-lg" />
                @elseif ($type === 'application/pdf')
                    <iframe src="{{ $url }}" class="h-[70vh] w-full rounded-lg" title="{{ $name }}"></iframe>
                @else
                    <div class="flex min-h-48 items-center justify-center rounded-lg border border-dashed border-zinc-300 bg-white text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        {{ __('laravel-attachments::attachments.no_preview_available') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</flux:modal>
