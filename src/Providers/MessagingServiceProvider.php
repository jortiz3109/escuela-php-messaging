<?php

namespace E4\Messaging\Providers;

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

        $this->app->singleton(MessagingApp::class, function ($app) {
            
            $config = $app->make('config')->get('messagingapp');
            $defaultConfig = $config['connections'][$config['default']];
            $defaultConfig['signature'] = file_get_contents($config['signature']);
            $defaultConfig['encryption'] = file_get_contents($config['encryption']);

            return new MessageBroker($defaultConfig);
        });
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
