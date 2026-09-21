<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Kongpda\LaravelAttachments\Providers\AttachmentLivewireServiceProvider;

it('registers package view namespaces', function (): void {
    expect(view()->exists('laravel-attachments::livewire.attachment-section'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.default.upload-section'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.flux.upload-section'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.default.preview-modal'))->toBeTrue()
        ->and(view()->exists('laravel-attachments::components.attachments.flux.preview-modal'))->toBeTrue();
});

it('publishes views and translations where their namespace looks for overrides', function (): void {
    $published = fn (string $tag): array => array_values(ServiceProvider::pathsToPublish(AttachmentLivewireServiceProvider::class, $tag));

    expect($published('attachments-livewire-views'))->toBe([resource_path('views/vendor/laravel-attachments')])
        ->and($published('attachments-livewire-translations'))->toBe([lang_path('vendor/laravel-attachments')]);
});

it('registers livewire components for package consumers', function (): void {
    expect(app('livewire')->exists('attachments-section'))->toBeTrue();
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

it('escapes delete confirmation warning copy', function (): void {
    config()->set('attachments.ui.driver', 'default');

    $html = Blade::render(
        '<x-attachments.delete-confirmation-modal :warning-text="$warning" />',
        ['warning' => '<script>alert(1)</script>'],
    );

    expect($html)->not->toContain('<script>')
        ->and($html)->toContain('alert(1)');
});
