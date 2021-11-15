<?php

namespace E4\Pigeon\Providers;

use E4\Pigeon\Events\DefaultMessageEvent;
use E4\Pigeon\Listeners\DefaultMessageListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class PigeonEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DefaultMessageEvent::class => [
            DefaultMessageListener::class,
        ],
    ];

    public function boot()
    {
    }
}
