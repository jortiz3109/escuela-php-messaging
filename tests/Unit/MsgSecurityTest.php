<?php

namespace Tests\Unit;

use E4\Messaging\Utils\MsgSecurity;
use Exception;
use Tests\TestCase;

class MsgSecurityTest extends TestCase
{
    private int $signerAlgorithm = OPENSSL_ALGO_SHA256;
    private $encryptSecretKey = 'CLASS-MESSAGE-KEY';
    private $encryptMethod = 'AES-256-CBC';
    private $encryptAlgorithm = 'sha256';

    private array $msgToPublic = [
        'envent' => 'user::created',
        'payload' => [
            'uuid' => 123,
            'name' => "Esperanza Gomez"
        ]
    ];

    public function test_it_prepare_a_message_to_send_to_the_messaging_queue()
    {
        $msgEncode = $this->createMsgSecurity()->prepareMsgToPublic($this->msgToPublic);
        $msgOut = json_decode($msgEncode, true);

        $this->assertIsNotArray($msgOut['payload']);
        $this->assertArrayHasKey('signature', $msgOut);
    }

    public function test_it_shows_error_when_the_structure_to_publish_is_wrong()
    {
        $this->expectException(Exception::class);
        $this->createMsgSecurity()->prepareMsgToPublic([
            'envent' => 'user::created',
            'payloadx' => []
        ]);
    }

    private function createMsgSecurity(): MsgSecurity
    {
        return new MsgSecurity(
            $this->encryptSecretKey,
            $this->encryptMethod,
            $this->encryptAlgorithm,
            $this->signerAlgorithm,
            file_get_contents(__DIR__ . '/Utils/Signature/privateKey.pem'),
            file_get_contents(__DIR__ . '/Utils/Signature/privateKey.pem')
        );
    }
}
