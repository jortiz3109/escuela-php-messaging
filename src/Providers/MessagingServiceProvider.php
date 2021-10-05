<?php

namespace E4\Messaging\Providers;

use Illuminate\Support\ServiceProvider;

class MessagingServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    public function register()
    {
        $this->registerResources();
    }


    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../../config/amqp.php' => config_path('amqp.php'),
        ], 'amqp-config');
    }

    protected function registerResources()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/amqp.php', 'amqp');
    }
}
