<div class="space-y-3 rounded-lg border border-red-200 bg-red-50 p-4">
    <div>
        <p class="text-sm font-medium text-red-900">{{ $title }}</p>
        <p class="text-sm text-red-700">{{ $subtitle }}</p>
    </div>

    <div class="rounded-md border border-red-200 bg-white p-3 text-sm text-zinc-700">
        <p class="font-medium text-red-700">{{ $warningHeading }}</p>
        <p class="mt-1">{!! $warningText !!}</p>
    </div>

    <div class="flex gap-3 text-sm">
        <button type="button" wire:click="{{ $wireClick }}" class="text-red-700">
            {{ __('laravel-attachments::attachments.delete_permanently') }}
        </button>
        @if ($cancelWireClick)
            <button type="button" wire:click="{{ $cancelWireClick }}" class="text-zinc-600">
                {{ __('laravel-attachments::attachments.cancel') }}
            </button>
        @endif
    </div>
</div>
