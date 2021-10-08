<?php

namespace E4\Messaging;

use E4\Messaging\Utils\MessageStructure;
use Illuminate\Support\Arr;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    private AMQPChannel $amqpChannel;
    private string $exchange;

    public function __construct(AMQPChannel $amqpChannel)
    {
        $this->amqpChannel = $amqpChannel;
        $this->exchange = Arr::get(config('amqp.exchange'), 'name');
    }

    public function publish(string $routingKey, string $id, string $event, array $body, array $properties = []): void
    {
        $message = json_encode(new MessageStructure($id, $event, $body));
        $this->amqpChannel->basic_publish($this->message($message, $properties), $this->exchange, $routingKey);
    }

    private function message(string $message, array $properties): AMQPMessage
    {
        return new AMQPMessage($message, $properties);
    }
}
