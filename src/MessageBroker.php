<?php

namespace E4\Messaging;

use Closure;
use E4\Messaging\AMQPConnection;
use E4\Messaging\Publisher;
use E4\Messaging\Utils\MessageStructure;
use E4\Messaging\Utils\MsgSecurity;

class MessageBroker
{
    private MsgSecurity $messageSecurity;
    private AMQPConnection $amqpConnection;
    private Publisher $publisher;
    private Consumer $consumer;

    public function __construct()
    {
        $this->messageSecurity = new MsgSecurity(
            config('amqp.encrypt.secretKey'),
            config('amqp.encrypt.method'),
            config('amqp.encrypt.algorithm'),
            config('amqp.signature.algorithm'),
            config('amqp.signature.publicKey'),
            config('amqp.signature.privateKey'),
        );
        $this->amqpConnection = new AMQPConnection();
        $this->publisher = new Publisher($this->amqpConnection->getChannel());
        $this->consumer = new Consumer($this->amqpConnection->getChannel());
    }

    public function publish(string $routingKey, string $event, array $body, string $id = '', array $properties = []): void
    {
        $message = $this->messageSecurity->prepareMsgToPublish(new MessageStructure($event, $body, $id));
        $this->publisher->publish($routingKey, $message, $properties);
    }

    public function consume(Closure $closure): void
    {
        $this->consumer->consume($closure);
    }
}
