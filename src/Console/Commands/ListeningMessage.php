<?php

namespace E4\Messaging\Console\Commands;

use E4\Messaging\Facades\Messaging;
use E4\Messaging\Utils\MsgSecurity;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class ListeningMessage extends Command
{

    protected $signature = 'messaging:listen';
    protected $description = 'Consume Rabbit Queue Messages';
    private $messageSecurity;

    public function __construct()
    {
        parent::__construct();

        $this->messageSecurity = new MsgSecurity(
            config('amqp.encrypt.secretKey'),
            config('amqp.encrypt.method'),
            config('amqp.encrypt.algorithm'),
            config('amqp.signature.algorithm'),
            config('amqp.signature.publicKey'),
            config('amqp.signature.privateKey'),
        );
    }

    public function handle()
    {
        Messaging::consume(function (AMQPMessage $message) {
            $this->error(json_encode($this->messageSecurity->prepareMsgToReceive($message->body)));
            $this->error($message->body);
            //$message->ack();
        });
    }
}
