<?php

namespace E4\Pigeon\Events;

use E4\Pigeon\AMQPMessageStructure;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DefaultMessageEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private AMQPMessageStructure $message;

    public function __construct(AMQPMessageStructure $message)
    {
        $this->message = $message;
    }

    public function message(): AMQPMessageStructure
    {
        return $this->message;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
