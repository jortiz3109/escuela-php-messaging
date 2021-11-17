<?php

namespace E4\Pigeon;

use Closure;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
    private bool $finish = false;
    private AMQPChannel $amqpChannel;
    private string $queue;
    private int $waitTimeout;

    public function __construct(string $queue, int $waitTimeout, AMQPChannel $amqpChannel)
    {
        $this->amqpChannel = $amqpChannel;
        $this->waitTimeout = $waitTimeout;
        $this->queue = $queue;
    }

    public function setQueue(string $queue): void
    {
        $this->queue = $queue;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function consume(Closure $closure): void
    {
        $this->amqpChannel
            ->basic_consume(
                $this->queue,
                '',
                false,
                false,
                false,
                false,
                function (AMQPMessage $message) use ($closure) {
                    $closure($message, $this);
                },
            );

        try {
            while ($this->amqpChannel->is_consuming() && false === $this->finish) {
                $this->amqpChannel->wait(null, false, $this->waitTimeout);
            }
            $this->amqpChannel->close();
        } catch (AMQPTimeoutException $ex) {
            $this->amqpChannel->close();
        }
    }

    public function stop(): void
    {
        $this->finish = true;
    }
}
