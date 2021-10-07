<?php

namespace E4\Messaging;

use E4\Messaging\Exceptions\AMQPConnectionException;
use Illuminate\Support\Arr;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AMQPConnection extends AMQPConnectionType
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct()
    {
        parent::__construct(config('amqp'));
        $this->setConnection();
    }

    /**
     * Get a AMQPStreamConnection form the current instance
     * 
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    /**
     * Get a AMQPChannel form the current instance
     * 
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    /**
     * Request a channel close and connection close
     * 
     * @return void
     * 
     * @throws AMQPTimeoutException If the channel connection time out was exceeded
     */
    public function shutdown(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Set connection in the constructor, validate config file and chose ssl o standard connection
     * 
     * @return void
     * 
     * @throws AMQPConnectionException if connection is failed
     */
    private function setConnection(): void
    {
        if (Arr::get($this->config, 'ssl', true)) {
            $this->connection = parent::getSSLConnection();
        } else {
            $this->connection = parent::getStreamConnection();
        }
        $this->connection->set_close_on_destruct(true);
        $this->channel = $this->connection->channel();
    }
}
