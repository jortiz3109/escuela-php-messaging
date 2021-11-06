<?php

namespace E4\Messaging;

use Closure;
use E4\Messaging\AMQPConnection;
use E4\Messaging\AMQPMessageStructure;
use E4\Messaging\Exceptions\MessageBrokerConfigException;
use E4\Messaging\Publisher;
use E4\Messaging\Utils\Helpers;
use E4\Messaging\Utils\MessageStructure;
use E4\Messaging\Utils\MsgSecurity;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class MessageBroker
{
    private MsgSecurity $messageSecurity;
    private AMQPConnection $amqpConnection;
    private Publisher $publisher;
    private Consumer $consumer;
    private array $config;

    public function __construct(array $config = null)
    {
        $this->config = $config ?? $this->configInit();
        $this->messageSecurity = new MsgSecurity(
            $this->config['encryption']['secretKey'],
            $this->config['encryption']['method'],
            $this->config['encryption']['algorithm'],
            $this->config['signature']['algorithm'],
            $this->config['signature']['publicKey'],
            $this->config['signature']['privateKey']
        );
        $this->amqpConnection = $this->createConnection();
        $this->publisher = new Publisher($this->config['exchange']['name'], $this->amqpConnection->getChannel());
        $this->consumer = new Consumer($this->config['queue']['name'], $this->amqpConnection->getChannel());
    }

    private function configInit(): array
    {
        $settings = config('messagingapp');
        $defaultConfig = $settings['connections'][$settings['default']];
        $defaultConfig['signature'] = $settings['signature'];
        $defaultConfig['signature']['publicKey'] = file_get_contents($settings['signature']['publicKey']);
        $defaultConfig['signature']['privateKey'] = file_get_contents($settings['signature']['privateKey']);
        $defaultConfig['encryption'] = $settings['encryption'];
        $defaultConfig['events'] = $settings['events'];
        return $defaultConfig;
    }

    protected function createConnection(): AMQPConnection
    {
        return new AMQPConnection($this->config);
    }

    private function validateConfig(array $newConfig): void
    {
        $missingKeys = Helpers::getMissingKeys($newConfig, $this->config);
        if ($missingKeys != []) {
            throw new MessageBrokerConfigException("Key invalid: " . json_encode($missingKeys));
        }
    }

    public function config(array $config): self
    {
        $newConfig = array_replace_recursive($this->config, $config);

        $this->validateConfig($newConfig);
        $class = get_class($this);
        return new $class($newConfig);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function publish(string $routingKey, string $event, array $body, string $id = '', array $properties = []): void
    {
        $message = $this->messageSecurity->prepareMsgToPublish(new MessageStructure($event, $body, $id));
        $this->publisher->publish($routingKey, $message, $properties);
    }

    public function consume(Closure $closure): void
    {
        $closureOut = function (AMQPMessage $amqpMessage) use ($closure) {
            $messageStructure = $this->messageSecurity->prepareMsgToReceive($amqpMessage->body);
            $msg = new AMQPMessageStructure($amqpMessage, $messageStructure);
            $closure($msg);
        };
        $this->consumer->consume($closureOut);
    }
}
