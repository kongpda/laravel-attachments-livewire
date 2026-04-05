@if ($url)
    <div class="space-y-3 rounded-lg border border-zinc-200 bg-zinc-50 p-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-zinc-900">{{ $name }}</p>
                <p class="text-xs text-zinc-500">{{ $type }}</p>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600">
                    {{ __('laravel-attachments::attachments.open') }}
                </a>
                <button type="button" wire:click="{{ $wireCloseMethod }}" class="text-zinc-600">
                    {{ __('laravel-attachments::attachments.cancel') }}
                </button>
            </div>
        </div>

        @if (str_starts_with($type, 'image/'))
            <img src="{{ $url }}" alt="{{ $name }}" class="mx-auto h-auto max-w-full rounded-lg shadow-sm" />
        @elseif ($type === 'application/pdf')
            <iframe src="{{ $url }}" class="h-[70vh] w-full rounded-lg bg-white" title="{{ $name }}"></iframe>
        @else
            <p class="text-sm text-zinc-500">{{ __('laravel-attachments::attachments.no_preview_available') }}</p>
        @endif
    </div>
@endif
