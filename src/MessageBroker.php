<?php

namespace E4\Pigeon;

use Closure;
use E4\Pigeon\Exceptions\MessageBrokerConfigException;
use E4\Pigeon\Utils\Helpers;
use E4\Pigeon\Utils\MessageStructure;
use E4\Pigeon\Utils\MsgSecurity;

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
        $settings = config('pigeon');
        $defaultConfig = $settings['connections'][$settings['default']];
        $defaultConfig['signature'] = $settings['signature'];
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
            throw new MessageBrokerConfigException('Key invalid: ' . json_encode($missingKeys));
        }
    }

    public function config(array $config): self
    {
        $newConfig = array_replace_recursive($this->config, $config);

        $this->validateConfig($newConfig);
        $class = get_class($this);
        return new $class($newConfig);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function publish(string $routingKey, array $body, string $id = '', array $properties = []): void
    {
        $message = $this->messageSecurity->prepareMsgToPublish(new MessageStructure($body, $id));
        $this->publisher->publish($routingKey, $message, $properties);
    }

    public function consume(Closure $closure): void
    {
        $this->consumer->consume($closure);
    }

    public function setQueue(string $queue): self
    {
        $this->consumer->setQueue($queue);
        return $this;
    }

    public function getMessageSecurity(): MsgSecurity
    {
        return $this->messageSecurity;
    }
}
