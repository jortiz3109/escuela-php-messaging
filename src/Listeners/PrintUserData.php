<?php

namespace App\Listeners;

use App\Events\DefaultMessageEvent;

class PrintUserData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewUserEvent  $event
     * @return void
     */
    public function handle(DefaultMessageEvent $event)
    {
        print_r($event->data());
    }
}
