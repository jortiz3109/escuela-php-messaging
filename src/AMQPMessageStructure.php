<?php

namespace E4\Messaging;

use E4\Messaging\Utils\MessageStructure;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPMessageStructure extends MessageStructure
{
    public AMQPMessage $AMQPMessage;

    public function __construct(AMQPMessage $AMQPMessage, MessageStructure $messageStructure)
    {
        parent::__construct($messageStructure->body, $messageStructure->id);
        $this->AMQPMessage = $AMQPMessage;
    }

    public function attributes(): array
    {
        return $this->AMQPMessage->get_properties();
    }

    public function ack(): void
    {
        $this->AMQPMessage->ack();
    }

    public function getRoutingKey(): string
    {
        return $this->AMQPMessage->getRoutingKey();
    }
}
