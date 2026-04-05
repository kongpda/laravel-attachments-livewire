@props([
    'wireModel' => 'attachments',
    'label' => null,
    'multiple' => true,
    'accept' => null,
    'heading' => __('laravel-attachments::attachments.upload_heading'),
    'helpText' => __('laravel-attachments::attachments.upload_help_text'),
    'error' => null,
])

@if (\Kongpda\LaravelAttachments\Support\AttachmentConfig::usesFluxUi())
    @include('laravel-attachments::components.attachments.flux.upload-section', [
        'wireModel' => $wireModel,
        'label' => $label,
        'multiple' => $multiple,
        'accept' => $accept,
        'heading' => $heading,
        'helpText' => $helpText,
        'error' => $error,
    ])
@else
    @include('laravel-attachments::components.attachments.default.upload-section', [
        'wireModel' => $wireModel,
        'label' => $label,
        'multiple' => $multiple,
        'accept' => $accept,
        'heading' => $heading,
        'helpText' => $helpText,
        'error' => $error,
    ])
@endif
