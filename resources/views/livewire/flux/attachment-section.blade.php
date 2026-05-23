<div class="space-y-4">
@if ($allowUpload)
    <x-attachments.upload-section
        wire-model="files"
        :multiple="true"
        :error="$uploadError"
    />
@endif

@if (count($attachments) > 0)
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($attachments as $attachment)
            @php
                $id = $attachment['id'];
                $fileName = $attachment['file_name'] ?? '';
                $fileSize = $attachment['file_size'] ?? null;
                $url = $attachment['url'] ?? '';
                $thumbnailUrl = $attachment['thumbnail_url'] ?? null;
                $isPreviewable = $attachment['is_previewable'] ?? false;
                $isImage = $attachment['is_image'] ?? false;
                $thumbOrImageUrl = $isImage ? $url : $thumbnailUrl ?? null;
                $captionValue = $captions[$id] ?? ($attachment['caption'] ?? '');
                $expanded = $expandedCaptions[$id] ?? false;
                $saved = $recentlySavedId === $id;
            @endphp

            <flux:card wire:key="attachment-card-{{ $id }}" class="group flex flex-col overflow-hidden p-0">
                @if ($showPreview && $isPreviewable)
                    <button
                        type="button"
                        class="flex h-24 w-full shrink-0 items-center justify-center overflow-hidden bg-zinc-100 transition hover:bg-zinc-200 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-inset dark:bg-zinc-700/50 dark:hover:bg-zinc-600/50"
                        wire:click="openPreview('{{ $id }}')"
                    >
                        @if ($thumbOrImageUrl)
                            <span class="relative flex h-full w-full items-center justify-center">
                                <img
                                    src="{{ $thumbOrImageUrl }}"
                                    alt=""
                                    class="h-full w-full object-contain"
                                    loading="lazy"
                                    onerror="
                                        this.classList.add('hidden');
                                        this.nextElementSibling?.classList.remove('hidden');
                                    "
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
                                    onerror="
                                        this.classList.add('hidden');
                                        this.nextElementSibling?.classList.remove('hidden');
                                    "
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
                        @if ($showDelete)
                            <flux:button
                                variant="ghost"
                                size="xs"
                                icon="trash"
                                class="shrink-0 text-zinc-500 hover:text-red-600"
                                wire:click.stop="confirmDeleteAttachment('{{ $id }}')"
                                title="{{ __('laravel-attachments::attachments.remove') }}"
                            />
                        @endif
                    </div>
                    @if ($fileSize !== null && $fileSize !== '')
                        <flux:text class="mt-0.5 text-xs text-zinc-500">
                            {{ number_format((int) $fileSize / 1024, 1) }} KB
                        </flux:text>
                    @endif

                    @if ($editable)
                        <div class="mt-3">
                            @if (! $expanded)
                                <button
                                    type="button"
                                    wire:click="expandCaption('{{ $id }}')"
                                    class="text-left text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300"
                                >
                                    {{ __('laravel-attachments::attachments.attachment_click_to_add_caption') }}
                                </button>
                            @else
                                <flux:input
                                    wire:model.blur="captions.{{ $id }}"
                                    wire:blur="saveCaption('{{ $id }}')"
                                    :placeholder="__('laravel-attachments::attachments.attachment_caption_placeholder')"
                                    size="sm"
                                    class="mt-0.5 min-w-0 flex-1"
                                    :clearable="false"
                                >
                                    @if ($saved || ! empty($captionValue))
                                        <x-slot name="iconTrailing">
                                            <span
                                                class="flex items-center gap-0.5"
                                                x-data
                                                x-init="@js($saved) && setTimeout(() => $wire.set('recentlySavedId', null), 3000)"
                                            >
                                                @if ($saved)
                                                    <flux:icon.check-circle class="size-5 shrink-0 text-green-500" />
                                                @endif
                                                @if (! empty($captionValue))
                                                    <flux:button
                                                        size="sm"
                                                        variant="subtle"
                                                        icon="x-mark"
                                                        class="-mr-1 shrink-0 text-zinc-500 hover:text-zinc-700"
                                                        wire:click.prevent.stop="confirmRemoveCaption('{{ $id }}')"
                                                        @mousedown.prevent
                                                        title="{{ __('laravel-attachments::attachments.remove_caption') }}"
                                                    />
                                                @endif
                                            </span>
                                        </x-slot>
                                    @endif
                                </flux:input>
                            @endif
                        </div>
                    @elseif ($captionValue)
                        <flux:text class="mt-1 line-clamp-2 text-xs text-zinc-500">{{ $captionValue }}</flux:text>
                    @endif
                </div>
            </flux:card>
        @endforeach
    </div>
@endif

<x-attachments.preview-modal
    modal-name="attachment-section-preview"
    :url="$previewUrl"
    :name="$previewName ?? ''"
    :type="$previewType ?? ''"
    wire-close-method="closePreview"
/>

<x-attachments.delete-confirmation-modal
    modal-name="attachment-section-delete"
    :file-name="$attachmentNameToDelete ?? ''"
    wire-click="performDelete"
    cancel-wire-click="cancelDeleteAttachment"
/>

<x-attachments.delete-confirmation-modal
    modal-name="attachment-section-remove-caption"
    :title="__('laravel-attachments::attachments.remove_caption')"
    :subtitle="__('laravel-attachments::attachments.remove_caption_confirm')"
    :warning-heading="__('laravel-attachments::attachments.remove_caption')"
    :warning-text="__('laravel-attachments::attachments.remove_caption_confirm')"
    wire-click="performRemoveCaption"
    cancel-wire-click="cancelRemoveCaption"
/>
</div>
