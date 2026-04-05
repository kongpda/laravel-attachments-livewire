<flux:file-upload
    wire:model="{{ $wireModel }}"
    :label="$label"
    :multiple="$multiple"
    :accept="$accept"
    :error="$error"
>
    <flux:file-upload.dropzone :heading="$heading" :text="$helpText" />
</flux:file-upload>

<div wire:loading wire:target="{{ $wireModel }}" class="flex items-center gap-2 text-sm text-zinc-500">
    <flux:icon.arrow-path class="size-4 animate-spin" />
    {{ __('laravel-attachments::attachments.uploading') }}
</div>
