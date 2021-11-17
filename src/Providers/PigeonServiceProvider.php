<?php

namespace E4\Pigeon\Providers;

use E4\Pigeon\Console\Commands\ListeningMessage;
use Illuminate\Support\ServiceProvider;

class PigeonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    public function register(): void
    {
        $this->registerResources();
        $this->commands([
            ListeningMessage::class,
        ]);
        $this->app->register(PigeonEventServiceProvider::class);
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/pigeon.php' => config_path('pigeon.php'),
        ], 'pigeon');
    }

    protected function registerResources(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/pigeon.php', 'pigeon');
    }
}
