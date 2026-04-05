@props([
    'file',
    'index' => 0,
    'removeWireClick' => null,
    'captionWireModel' => null,
    'captionPlaceholder' => null,
    'initialCaption' => '',
    'previewWireClick' => null,
])

@php
    $previewUrl = \Kongpda\LaravelAttachments\Support\AttachmentUi::resolveTemporaryPreviewUrl($file);
    $isImage = \Kongpda\LaravelAttachments\Support\AttachmentUi::isImageUpload($file);
@endphp

@if (\Kongpda\LaravelAttachments\Support\AttachmentConfig::usesFluxUi())
    @include('laravel-attachments::components.attachments.flux.pending-card', [
        'file' => $file,
        'index' => $index,
        'removeWireClick' => $removeWireClick,
        'captionWireModel' => $captionWireModel,
        'captionPlaceholder' => $captionPlaceholder,
        'initialCaption' => $initialCaption,
        'previewWireClick' => $previewWireClick,
        'previewUrl' => $previewUrl,
        'isImage' => $isImage,
    ])
@else
    @include('laravel-attachments::components.attachments.default.pending-card', [
        'file' => $file,
        'index' => $index,
        'removeWireClick' => $removeWireClick,
        'captionWireModel' => $captionWireModel,
        'captionPlaceholder' => $captionPlaceholder,
        'initialCaption' => $initialCaption,
        'previewWireClick' => $previewWireClick,
        'previewUrl' => $previewUrl,
        'isImage' => $isImage,
    ])
@endif
