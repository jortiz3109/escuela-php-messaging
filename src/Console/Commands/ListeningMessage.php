<?php

namespace E4\Pigeon\Console\Commands;

use E4\Pigeon\AMQPMessageStructure;
use E4\Pigeon\Events\DefaultMessageEvent;
use E4\Pigeon\Facades\Pigeon;
use E4\Pigeon\Utils\MsgSecurity;
use Exception;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class ListeningMessage extends Command
{
    protected $signature = 'pigeon:listen {queue?}';
    protected $description = 'Receives messages from the rabbitmq queue';
    private MsgSecurity $messageSecurity;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->messageSecurity = Pigeon::getMessageSecurity();
        $this->line('Start to receive messages');
        $this->newLine();

        $queue = $this->argument('queue');
        try {
            if ($queue != null) {
                $this->line('Set optional queue ' . $queue);
                $this->newLine();
                Pigeon::setQueue($queue)->consume(function (AMQPMessage $message) {
                    $this->consumeProcess($message);
                });
            } else {
                Pigeon::consume(function (AMQPMessage $message) {
                    $this->consumeProcess($message);
                });
            }
        } catch (Exception $exception) {
            report($exception);
            $this->error('Exception: ' . $exception);
            $this->error('Something went wrong');
        }

        $this->info('The command finish');
    }

    private function consumeProcess(AMQPMessage $message): void
    {
        $events = Pigeon::getConfig()['events'];
        try {
            $data = $this->messageSecurity->prepareMsgToReceive($message->body);
            $msg = new AMQPMessageStructure($message, $data);

            if (array_key_exists($message->getRoutingKey(), $events)) {
                $this->line('Dispatch event: ' . $message->getRoutingKey());
                event(new $events[$message->getRoutingKey()]($msg));
            } else {
                $this->line('Dispatch event: DefaultMessageEvent');
                event(new DefaultMessageEvent($msg));
                $this->error("There aren't event");
            }
        } catch (Exception $exception) {
            report($exception);
            $this->error('Exception: ' . $exception);
        }

        $this->newLine();
    }
}
