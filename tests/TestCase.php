<?php

declare(strict_types=1);

namespace Kongpda\LaravelAttachments\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kongpda\LaravelAttachments\Providers\AttachmentCoreServiceProvider;
use Kongpda\LaravelAttachments\Providers\AttachmentLivewireServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Kongpda\\LaravelAttachments\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        // Testbench does not auto-run the core package's .php.stub migration.
        (include __DIR__.'/../vendor/kongpda/laravel-attachments-core/database/migrations/create_attachments_table.php.stub')->up();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            AttachmentCoreServiceProvider::class,
            AttachmentLivewireServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('app.key', 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=');
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
