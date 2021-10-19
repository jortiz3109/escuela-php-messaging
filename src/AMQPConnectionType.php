<?php

namespace E4\Messaging;

use Illuminate\Support\Arr;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use ValueError;

abstract class AMQPConnectionType
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function getStreamConnection(): AMQPStreamConnection
    {
        $host = $this->getHostConfig();
        return new AMQPStreamConnection(
            Arr::get($host, 'host'),
            Arr::get($host, 'port'),
            Arr::get($host, 'username'),
            Arr::get($host, 'password'),
            Arr::get($host, 'vhost')
        );
    }

    protected function getSSLConnection(): AMQPSSLConnection
    {
        $sslOptions = array_filter(Arr::get($this->config, 'ssl_options', []), function ($item) {
            return null !== $item;
        });

        $host = $this->getHostConfig();
        return new AMQPSSLConnection(
            Arr::get($host, 'host'),
            Arr::get($host, 'port'),
            Arr::get($host, 'user'),
            Arr::get($host, 'password'),
            Arr::get($host, 'vhost'),
            $sslOptions
        );
    }

    private function getHostConfig(): array
    {
        $host = Arr::get($this->config, 'host');
        if ($host != null) {
            return $host;
        }
        throw new ValueError('The host is null please validate the config');
    }
}
