<?php

namespace E4\Messaging;

use E4\Messaging\Utils\MessageStructure;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPMessageStructure extends MessageStructure
{
    public AMQPMessage $AMQPMessage;

    public function __construct(AMQPMessage $AMQPMessage, MessageStructure $messageStructure)
    {
        $this->event = $messageStructure->event;
        $this->body = $messageStructure->body;
        $this->AMQPMessage = $AMQPMessage;
    }

    function attributes(): array
    {
        return $this->AMQPMessage->get_properties();
    }

    public function ack(): void
    {
        $this->AMQPMessage->ack();
    }
}
