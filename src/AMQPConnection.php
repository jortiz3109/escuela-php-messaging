<?php

namespace E4\Messaging;

use E4\Messaging\Exceptions\AMQPConnectionException;
use Exception;
use Illuminate\Support\Arr;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AMQPConnection
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private array $config;

    public function __construct()
    {
        $this->config = config('amqp');
        $this->setConnection();
    }

    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function shutdown(): void
    {
        try {
            $this->channel->close();
            $this->connection->close();
        } catch (Exception $e) {
            throw new AMQPConnectionException("Error to close connection -> " . $e->getMessage());
        }
    }

    private function setConnection(): void
    {
        try {
            if (!is_null($this->config)) {
                Arr::get($this->config, 'ssl', true)
                    ? $this->setSSLConnection()
                    : $this->setStreamConnection();

                $this->connection->set_close_on_destruct(true);
                $this->channel = $this->connection->channel();
            } else {
                throw new AMQPConnectionException('Error -> Please publish the config file by running php artisan vendor:publish --tag=amqp-config');
            }
        } catch (Exception $ex) {
            throw new AMQPConnectionException("Error ->" . $ex->getMessage());
        }
    }

    private function setStreamConnection()
    {
        $host = $this->getHostConfig();
        $this->connection = new AMQPStreamConnection(
            Arr::get($host, 'host'),
            Arr::get($host, 'port'),
            Arr::get($host, 'username'),
            Arr::get($host, 'password'),
            Arr::get($host, 'vhost')
        );
        $this->channel = $this->connection->channel();
        return $this->connection;
    }

    private function setSSLConnection()
    {
        $sslOptions = array_filter(Arr::get($this->config, 'ssl_options', []), function ($item) {
            return null !== $item;
        });

        $host = $this->getHostConfig();
        $this->connection = new AMQPSSLConnection(
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
