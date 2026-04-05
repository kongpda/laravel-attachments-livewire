@props([
    'modalName' => 'confirm-delete-attachment',
    'title' => __('laravel-attachments::attachments.delete_attachment_title'),
    'subtitle' => __('laravel-attachments::attachments.delete_attachment_subtitle'),
    'warningHeading' => __('laravel-attachments::attachments.delete_attachment_warning_heading'),
    'warningText' => null,
    'fileName' => '',
    'wireClick' => 'deleteAttachment',
    'cancelWireClick' => null,
])

@php
    $resolvedWarningText = $warningText ?? __('laravel-attachments::attachments.delete_attachment_warning_text', ['name' => e($fileName)]);
@endphp

@if (\Kongpda\LaravelAttachments\Support\AttachmentConfig::usesFluxUi())
    @include('laravel-attachments::components.attachments.flux.delete-confirmation-modal', [
        'modalName' => $modalName,
        'title' => $title,
        'subtitle' => $subtitle,
        'warningHeading' => $warningHeading,
        'warningText' => $resolvedWarningText,
        'fileName' => $fileName,
        'wireClick' => $wireClick,
        'cancelWireClick' => $cancelWireClick,
    ])
@else
    @include('laravel-attachments::components.attachments.default.delete-confirmation-modal', [
        'modalName' => $modalName,
        'title' => $title,
        'subtitle' => $subtitle,
        'warningHeading' => $warningHeading,
        'warningText' => $resolvedWarningText,
        'fileName' => $fileName,
        'wireClick' => $wireClick,
        'cancelWireClick' => $cancelWireClick,
    ])
@endif
