<?php

namespace E4\Messaging;

use Closure;
use E4\Messaging\Utils\MsgSecurity;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
    private bool $finish = false;
    private AMQPChannel $amqpChannel;
    private string $queue;

    public function __construct(AMQPChannel $amqpChannel)
    {
        $this->amqpChannel = $amqpChannel;
        $this->queue = config('amqp.queue.name');
    }

    public function setQueue(string $queue)
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
            while ($this->amqpChannel->callbacks && false === $this->finish) {
                $this->amqpChannel->wait(null, false, 3);
            }
            $this->amqpChannel->close();
        } catch (AMQPTimeoutException $ex) {
            $this->amqpChannel->close();
        }
    }

    public function stop()
    {
        $this->finish = true;
    }
}
