<?php

namespace E4\Messaging\Providers;

use Illuminate\Support\ServiceProvider;

class MessagingServiceProvider extends ServiceProvider
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
    }


    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/amqp.php' => config_path('amqp.php'),
        ], 'amqp-config');
    }

    protected function registerResources(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/amqp.php', 'amqp');
    }
}
