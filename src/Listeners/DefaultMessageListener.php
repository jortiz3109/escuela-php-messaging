<?php

namespace E4\Messaging\Listeners;

use E4\Messaging\Events\DefaultMessageEvent;

class DefaultMessageListener
{
    public function __construct()
    {
        //
    }

    public function handle(DefaultMessageEvent $event)
    {
        print_r($event->message());
        $event->message()->ack();
    }
}
