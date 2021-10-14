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

    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    /**
     * @throws AMQPTimeoutException If the channel connection time out was exceeded
     */
    public function shutdown(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @throws AMQPConnectionException if connection is failed
     */
    private function setConnection(): void
    {
        $this->connection = Arr::get($this->config, 'ssl', true) ?
            $this->connection = parent::getSSLConnection() :
            $this->connection = parent::getStreamConnection();

        $this->connection->set_close_on_destruct(true);
        $this->channel = $this->connection->channel();
    }
}
