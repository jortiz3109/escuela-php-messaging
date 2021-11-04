<?php

namespace E4\Messaging;

use E4\Messaging\Utils\MessageStructure;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    private AMQPChannel $amqpChannel;
    private string $exchange;

    public function __construct(string $exchange, AMQPChannel $amqpChannel)
    {
        $this->amqpChannel = $amqpChannel;
        $this->exchange = $exchange;
    }

    public function publish(string $routingKey, string $message, array $properties = []): void
    {
        $this->amqpChannel->basic_publish($this->message($message, $properties), $this->exchange, $routingKey);
    }

    private function message(string $message, array $properties): AMQPMessage
    {
        return new AMQPMessage($message, $properties);
    }
}
