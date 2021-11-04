<?php

namespace Tests\Feature;

use E4\Messaging\AMQPConnection;
use E4\Messaging\Exceptions\MessageBrokerConfigException;
use E4\Messaging\MessageBroker;
use PhpAmqpLib\Channel\AMQPChannel;
use Tests\TestCase;

class MessageBrokerTest extends TestCase
{
    public function test_it_can_change_the_messagebroker_settings(): void
    {
        $messageBroker = $this->getMessageBroker($this->getConfig());
        $newMessageBroker = $messageBroker->config([
            'host' => [
                'username' => 'newUserName',
                'password' => 'newPassword',
            ]
        ]);
        $this->assertInstanceOf(MessageBroker::class, $newMessageBroker);
        $this->assertEquals('newUserName', $newMessageBroker->getConfig()['host']['username']);
        $this->assertEquals('newPassword', $newMessageBroker->getConfig()['host']['password']);
    }

    public function test_it_shows_error_when_configuration_is_wrong(): void
    {
        $badConfig = [
            'host' => [
                'usernamex' => 'newUserName',
            ]
        ];
        $this->expectException(MessageBrokerConfigException::class);
        $this->expectExceptionMessage('Key invalid: ' . json_encode($badConfig));

        $messageBroker = $this->getMessageBroker($this->getConfig());
        $messageBroker->config($badConfig);
    }

    private function getConfig(string $connection = 'rabbitmq'): array
    {
        $config = config('messagingapp');
        $config['signature']['publicKey'] = file_get_contents(__DIR__ . '/../certs/signaturePublicKey.pem');
        $config['signature']['privateKey'] = file_get_contents(__DIR__ . '/../certs/signaturePrivateKey.pem');
        $configMB = $config['connections'][$connection];
        $configMB['signature'] = $config['signature'];
        $configMB['encryption'] = $config['encryption'];
        return $configMB;
    }

    private function getMessageBroker(array $configMB)
    {
        $messageBrokerMock = $this->getMockBuilder(MessageBroker::class)
            ->setConstructorArgs([$configMB])
            ->onlyMethods(['createConnection'])
            ->getMock();

        $amqpConnectionMock = $this->getMockBuilder(AMQPConnection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getChannel'])
            ->getMock();

        $amqpChannelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $amqpConnectionMock->method('getChannel')->willReturn($amqpChannelMock);
        $messageBrokerMock->method('createConnection')->willReturn($amqpConnectionMock);

        return $messageBrokerMock;
    }
}
