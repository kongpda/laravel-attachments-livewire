<div class="space-y-3">
    @if ($label)
        <label class="block text-sm font-medium text-zinc-900">{{ $label }}</label>
    @endif

    <label class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-zinc-300 bg-zinc-50 px-6 py-8 text-center transition hover:border-zinc-400 hover:bg-zinc-100">
        <span class="text-sm font-medium text-zinc-900">{{ $heading }}</span>
        <span class="mt-1 text-xs text-zinc-500">{{ $helpText }}</span>
        <input
            type="file"
            class="sr-only"
            wire:model="{{ $wireModel }}"
            @if ($multiple) multiple @endif
            @if ($accept) accept="{{ $accept }}" @endif
        />
    </label>

    @if ($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif

    <div wire:loading wire:target="{{ $wireModel }}" class="flex items-center gap-2 text-sm text-zinc-500">
        <svg class="h-4 w-4 animate-spin text-zinc-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
        </svg>
        {{ __('laravel-attachments::attachments.uploading') }}
    </div>
</div>
