@props([
    'url' => '',
    'thumbnailUrl' => null,
    'fileName' => '',
    'fileSize' => null,
    'caption' => null,
    'description' => null,
    'isPreviewable' => false,
    'isImage' => false,
    'showPreview' => true,
    'showDownload' => true,
    'showDelete' => false,
    'previewWireClick' => null,
    'deleteWireClick' => null,
    'markedForRemoval' => false,
    'undoRemovalWireClick' => null,
])

@php
    $resolvedCaption = $caption ?? $description;
@endphp

@if (\Kongpda\LaravelAttachments\Support\AttachmentConfig::usesFluxUi())
    @include('laravel-attachments::components.attachments.flux.preview-card', [
        'url' => $url,
        'thumbnailUrl' => $thumbnailUrl,
        'fileName' => $fileName,
        'fileSize' => $fileSize,
        'resolvedCaption' => $resolvedCaption,
        'isPreviewable' => $isPreviewable,
        'isImage' => $isImage,
        'showPreview' => $showPreview,
        'showDownload' => $showDownload,
        'showDelete' => $showDelete,
        'previewWireClick' => $previewWireClick,
        'deleteWireClick' => $deleteWireClick,
        'markedForRemoval' => $markedForRemoval,
        'undoRemovalWireClick' => $undoRemovalWireClick,
    ])
@else
    @include('laravel-attachments::components.attachments.default.preview-card', [
        'url' => $url,
        'thumbnailUrl' => $thumbnailUrl,
        'fileName' => $fileName,
        'fileSize' => $fileSize,
        'resolvedCaption' => $resolvedCaption,
        'isPreviewable' => $isPreviewable,
        'isImage' => $isImage,
        'showPreview' => $showPreview,
        'showDownload' => $showDownload,
        'showDelete' => $showDelete,
        'previewWireClick' => $previewWireClick,
        'deleteWireClick' => $deleteWireClick,
        'markedForRemoval' => $markedForRemoval,
        'undoRemovalWireClick' => $undoRemovalWireClick,
    ])
@endif
