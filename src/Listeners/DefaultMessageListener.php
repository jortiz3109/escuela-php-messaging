<?php

namespace E4\Pigeon\Listeners;

use E4\Pigeon\Events\DefaultMessageEvent;

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
