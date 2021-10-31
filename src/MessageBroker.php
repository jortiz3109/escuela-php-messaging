<?php

namespace E4\Messaging;

use E4\Messaging\AMQPConnection;
use E4\Messaging\Publisher;

class MessageBroker
{
    private AMQPConnection $amqpConnection;
    private Publisher $publisher;

    public function __construct()
    {
        $this->amqpConnection = new AMQPConnection();
        $this->publisher = new Publisher($this->amqpConnection->getChannel());
    }

    public function publish(string $routingKey, string $event, array $body, string $id = null, array $properties = []): void
    {
        $messageSecurity = new MsgSecurity(
            config('amqp.encrypt.secretKey'),
            config('amqp.encrypt.method'),
            config('amqp.encrypt.algorithm'),
            config('amqp.signature.algorithm'),
            config('amqp.signature.publicKey'),
            config('amqp.signature.privateKey'),
        );
        $message = $messageSecurity->prepareMsgToPublish(new MessageStructure($event, $body, $id));
        $this->publisher->publish($routingKey, $message, $properties);
    }
}