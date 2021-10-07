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

    /**
     * Get a standard stream connection with config file info.
     * 
     * @return AMQPStreamConnection
     */
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

    /**
     * Get a SSL connection with config file info.
     * 
     * @return AMQPSSLConnection
     */
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

    /**
     * Get host info in the config file
     * 
     * @return array with host info
     */
    private function getHostConfig(): array
    {
        return Arr::get($this->config, 'host');
    }
}
