<?php

namespace E4\Messaging\Console\Commands;

use E4\Messaging\AMQPMessageStructure;
use E4\Messaging\Events\DefaultMessageEvent;
use E4\Messaging\Facades\Messaging;
use E4\Messaging\Utils\MsgSecurity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class ListeningMessage extends Command
{
    protected string $signature = 'messaging:listen {queue?}';
    protected string $description = 'Receives messages from the rabbitmq queue';
    private MsgSecurity $messageSecurity;

    public function __construct()
    {
        parent::__construct();
        $this->messageSecurity = Messaging::getMessageSecurity();
    }

    public function handle()
    {
        $this->line('Start to receive messages');
        $this->newLine();

        $queue = $this->argument('queue');
        try {
            if ($queue != null) {
                $this->line('Set optional queue ' . $queue);
                $this->newLine();
                Messaging::setQueue($queue)->consume(function (AMQPMessage $message) {
                    $this->consumeProcess($message);
                });
            } else {
                Messaging::consume(function (AMQPMessage $message) {
                    $this->consumeProcess($message);
                });
            }
        } catch (\Exception $exception) {
            Log::error('Exception command messaging:listen ' . $exception);
            $this->error('Exception: ' . $exception);
            $this->error('Something went wrong');
        }

        $this->info('The command finish');
    }

    private function consumeProcess(AMQPMessage $message)
    {
        $this->line('Dispatch event:');
        $events = config('messagingapp.events');
        $this->line('Event: ' . $message->getRoutingKey());
        $this->newLine();

        try {
            $this->line('Prepare MsgStructure:');
            $data = $this->messageSecurity->prepareMsgToReceive($message->body);
            $msg = new AMQPMessageStructure($message, $data);
            if (array_key_exists($message->getRoutingKey(), $events)) {
                event(new $events[$message->getRoutingKey()]($msg));
            } else {
                event(new DefaultMessageEvent($msg));
                $this->error("There aren't event");
            }
        } catch (\Exception $exception) {
            report($exception);
            $this->error('Exception: ' . $exception);
        }

        $this->newLine();
    }
}
