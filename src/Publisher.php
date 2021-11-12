<?php

namespace E4\Messaging;

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

    public function publish(string $message, array $properties = []): void
    {
        $this->amqpChannel->basic_publish($this->message($message, $properties), $this->exchange);
    }

    private function message(string $message, array $properties): AMQPMessage
    {
        return new AMQPMessage($message, $properties);
    }
}
