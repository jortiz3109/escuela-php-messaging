<?php

namespace E4\Messaging\Providers;

use E4\Messaging\Console\Commands\ListeningMessage;
use E4\Messaging\MessageBroker;
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
        $this->commands([
            ListeningMessage::class
        ]);
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/messagingapp.php' => config_path('messagingapp.php'),
        ], 'messagingapp');
    }

    protected function registerResources(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/messagingapp.php', 'messagingapp');
    }
}
