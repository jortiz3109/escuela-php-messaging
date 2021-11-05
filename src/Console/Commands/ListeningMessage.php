<?php

namespace E4\Messaging\Console\Commands;

use E4\Messaging\Exceptions\SignatureVerifyException;
use E4\Messaging\MessageBroker;
use E4\Messaging\Utils\MsgSecurity;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Exception;

class ListeningMessage extends Command
{

    protected $signature = 'messaging:listen';
    protected $description = 'Consume Rabbit Queue Messages';
    private MsgSecurity $messageSecurity;

    public function __construct()
    {
        parent::__construct();
        try {
            $this->messageSecurity = new MsgSecurity(
                config('messagingapp.encryption.secretKey'),
                config('messagingapp.encryption.method'),
                config('messagingapp.encryption.algorithm'),
                config('messagingapp.signature.algorithm'),
                file_get_contents(config('messagingapp.signature.publicKey')),
                file_get_contents(config('messagingapp.signature.privateKey')),
            );
        } catch (\Exception $exception) {
            report($exception);
            return false;
        }
    }

    public function handle(MessageBroker $messageBroker)
    {
        if(!empty($messageBroker)) {
            try {
                $messageBroker->consume(/**
                 * @param AMQPMessage $message
                 * @throws SignatureVerifyException
                 */ function (AMQPMessage $message) {
                    $this->error(json_encode($this->messageSecurity->prepareMsgToReceive($message->body)));
                    $this->error($message->body);
                    //$message->ack();
                });
            } catch (Exception $exception) {
                report($exception);
                return false;
            }
        }
    }
}
