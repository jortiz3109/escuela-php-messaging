<?php

namespace App\Events;

use E4\Messaging\AMQPMessageStructure;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DefaultMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private AMQPMessageStructure $message;

    public function __construct(AMQPMessageStructure $message)
    {
        $this->message = $message;
    }

    public function data()
    {
        return $this->data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
