<?php

namespace E4\Messaging;

use Illuminate\Support\Arr;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

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
        return Arr::get($this->config, 'host');
    }
}
