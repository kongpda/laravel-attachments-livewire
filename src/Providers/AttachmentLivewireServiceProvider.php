<?php

declare(strict_types=1);

namespace Kongpda\LaravelAttachments\Providers;

use Illuminate\Support\Facades\Blade;
use Kongpda\LaravelAttachments\Livewire\AttachmentSection;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class AttachmentLivewireServiceProvider extends PackageServiceProvider
{
    private const string VIEW_NAMESPACE = 'laravel-attachments';

    public function configurePackage(Package $package): void
    {
        $package->name('laravel-attachments-livewire');
    }

    public function bootingPackage(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', self::VIEW_NAMESPACE);
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', self::VIEW_NAMESPACE);

        // Published where the `laravel-attachments` namespace looks for overrides.
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/'.self::VIEW_NAMESPACE),
        ], 'attachments-livewire-views');
        $this->publishes([
            __DIR__.'/../../resources/lang' => $this->app->langPath('vendor/'.self::VIEW_NAMESPACE),
        ], 'attachments-livewire-translations');
        Blade::anonymousComponentPath(__DIR__.'/../../resources/views/components');
        Blade::anonymousComponentPath(__DIR__.'/../../resources/views/components/attachments', 'attachments');

        if ((bool) config('attachments.livewire.register_components', true) && class_exists(Livewire::class)) {
            Livewire::component('attachments-section', AttachmentSection::class);
        }
    }
}
