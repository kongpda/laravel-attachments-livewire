@props([
    'modalName' => 'preview-attachment',
    'url' => null,
    'name' => '',
    'type' => '',
    'wireCloseMethod' => 'closePreview',
])

@if (\Kongpda\LaravelAttachments\Support\AttachmentConfig::usesFluxUi())
    @include('laravel-attachments::components.attachments.flux.preview-modal', [
        'modalName' => $modalName,
        'url' => $url,
        'name' => $name,
        'type' => $type,
        'wireCloseMethod' => $wireCloseMethod,
    ])
@else
    @include('laravel-attachments::components.attachments.default.preview-modal', [
        'modalName' => $modalName,
        'url' => $url,
        'name' => $name,
        'type' => $type,
        'wireCloseMethod' => $wireCloseMethod,
    ])
@endif
