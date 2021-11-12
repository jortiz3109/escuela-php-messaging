<?php

namespace E4\Messaging\Providers;

use E4\Messaging\Events\DefaultMessageEvent;
use E4\Messaging\Listeners\DefaultMessageListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class MessagingEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DefaultMessageEvent::class => [
            DefaultMessageListener::class
        ]
    ];

    public function boot()
    {
    }
}
