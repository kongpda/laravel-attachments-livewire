<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Kongpda\LaravelAttachments\Livewire\AttachmentEditCard;
use Kongpda\LaravelAttachments\Livewire\AttachmentSection;

it('registers package view namespaces', function (): void {
    expect(view()->exists('laravel-attachments::livewire.attachment-section'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::livewire.attachment-edit-card'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.default.upload-section'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.flux.upload-section'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.default.preview-modal'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.flux.preview-modal'))->toBeTrue();
});

it('registers livewire components for package consumers', function (): void {
    expect(app('livewire')->getClass('attachments-section'))->toBe(AttachmentSection::class)
        ->and(app('livewire')->getClass('attachments-edit-card'))->toBe(AttachmentEditCard::class);
});

it('renders anonymous attachment components when default ui is enabled', function (): void {
    config()->set('attachments.ui.driver', 'default');

    $html = Blade::render('<x-attachments.upload-section wire-model="attachments" heading="Upload now" help-text="Helpful text" />');

    expect($html)->toContain('Upload now')
        ->and($html)->toContain('Helpful text')
        ->and($html)->toContain('wire:model="attachments"');
});

it('renders namespaced anonymous attachment components when default ui is enabled', function (): void {
    config()->set('attachments.ui.driver', 'default');

    $html = Blade::render('<x-attachments::upload-section wire-model="attachments" heading="Upload now" help-text="Helpful text" />');

    expect($html)->toContain('Upload now')
        ->and($html)->toContain('Helpful text')
        ->and($html)->toContain('wire:model="attachments"');
});
