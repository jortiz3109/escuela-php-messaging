<?php

namespace E4\Pigeon\Console\Commands;

use E4\Pigeon\AMQPMessageStructure;
use E4\Pigeon\Events\DefaultMessageEvent;
use E4\Pigeon\Exceptions\DecryptMethodException;
use E4\Pigeon\Exceptions\SignatureVerifyException;
use E4\Pigeon\Facades\Pigeon;
use E4\Pigeon\Utils\MsgSecurity;
use Illuminate\Console\Command;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class ListeningMessage extends Command
{
    protected $signature = 'pigeon:listen 
    {queue?} 
    {--continue-in-error}';
    protected $description = 'Receives messages from the rabbitmq queue';
    private MsgSecurity $messageSecurity;

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
        } catch (AMQPTimeoutException $exception) {
            report($exception);
            $this->error('Exception: ' . $exception);
            $this->error('Something went wrong');
            $this->checkBehavior();
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
        } catch (SignatureVerifyException | DecryptMethodException $exception) {
            report($exception);
            $this->error('Exception: ' . $exception);
            $this->checkBehavior();
        }

        $this->newLine();
    }

    private function checkBehavior(): void
    {
        if (!$this->option('continue-in-error')) {
            exit('Continue In Error Disable Ending...');
        }
    }
}
