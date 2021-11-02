<?php

namespace E4\Messaging;

use Illuminate\Support\Arr;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class AMQPConnection extends AMQPConnectionType
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(array $config)
    {
        parent::__construct($config);
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
     * @throws AMQPTimeoutException|\Exception
     */
    public function shutdown(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    private function setConnection(): void
    {
        $this->connection = Arr::get($this->config, 'ssl', true) ?
            $this->connection = parent::getSSLConnection() :
            $this->connection = parent::getStreamConnection();

        $this->connection->set_close_on_destruct(true);
        $this->channel = $this->connection->channel();
    }
}
