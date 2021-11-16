<?php

namespace E4\Pigeon\Listeners;

use E4\Pigeon\Events\DefaultMessageEvent;

class DefaultMessageListener
{
    public function __construct()
    {
    }

    public function handle(DefaultMessageEvent $event): void
    {
        $event->message()->ack();
    }
}
