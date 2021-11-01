<?php

namespace App\Console\Commands;

use E4\Messaging\Facades\Messaging;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeMessage extends Command
{

    protected $signature = 'consume:message';

    protected $description = 'Consume Rabbit Queue Messages';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Messaging::consume(function (AMQPMessage $message) {
            $this->error($message->body);
            $message->ack();
        });
    }
}
