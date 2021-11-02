<?php

namespace Tests\Unit;

use E4\Messaging\Consumer;
use PhpAmqpLib\Channel\AMQPChannel;
use Tests\TestCase;

class ConsumerTest extends TestCase
{
    public function test_it_can_construct_a_consumer(): void
    {
        $consumer = new Consumer('queueName', $this->prepareAMQPChannel());

        $this->assertNotNull($consumer);
    }

    public function test_it_can_set_queue_name(): void
    {
        $consumer = new Consumer('queueName', $this->prepareAMQPChannel());
        $consumer->setQueue('queue');

        $this->assertEquals('queue', $consumer->getQueue());
    }

    public function test_it_can_consume(): void
    {
        $consumer = new Consumer('queueName', $this->prepareAMQPChannel());
        $consumer->setQueue('queue');
        $consumer->consume(function () {
        });
        $this->assertTrue(true);
    }

    protected function prepareAMQPChannel()
    {
        return $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
