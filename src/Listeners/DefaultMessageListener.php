<?php

namespace E4\Messaging\Listeners;

use E4\Messaging\Events\DefaultMessageEvent;

class DefaultMessageListener
{
    public function __construct()
    {
        // Do nothing because is a listener.
    }

    public function handle(DefaultMessageEvent $event): void
    {
//        print_r($event->message()); //  Exception: Memory exhaust
        $event->message()->ack();
    }
}
