<flux:modal :name="$modalName" class="max-w-md">
    <div class="space-y-4">
        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <flux:icon.trash class="size-6 text-red-600 dark:text-red-400" />
            </div>
            <div>
                <flux:heading size="lg">{{ $title }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $subtitle }}</flux:text>
            </div>
        </div>

        <flux:callout variant="danger" icon="exclamation-triangle">
            <flux:callout.heading>{{ $warningHeading }}</flux:callout.heading>
            <flux:callout.text>{{ $warningText }}</flux:callout.text>
        </flux:callout>

        <div class="flex justify-end gap-3 pt-2">
            <flux:modal.close>
                @if ($cancelWireClick)
                    <flux:button variant="ghost" wire:click="{{ $cancelWireClick }}">
                        {{ __('laravel-attachments::attachments.cancel') }}
                    </flux:button>
                @else
                    <flux:button variant="ghost">
                        {{ __('laravel-attachments::attachments.cancel') }}
                    </flux:button>
                @endif
            </flux:modal.close>
            <flux:button
                variant="danger"
                wire:click="{{ $wireClick }}"
                x-on:click="$flux.modal('{{ $modalName }}').close()"
            >
                {{ __('laravel-attachments::attachments.delete_permanently') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
