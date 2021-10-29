<?php

namespace Tests\Unit;

use E4\Messaging\Utils\MessageStructure;
use E4\Messaging\Utils\MsgSecurity;
use Exception;
use Tests\TestCase;

class MsgSecurityTest extends TestCase
{
    private int $signerAlgorithm = OPENSSL_ALGO_SHA256;
    private $encryptSecretKey = 'CLASS-MESSAGE-KEY';
    private $encryptMethod = 'AES-256-CBC';
    private $encryptAlgorithm = 'sha256';
    private array $msgBody = [
        'user' => [
            'uuid' => 123,
            'name' => "Esperanza Gomez"
        ]
    ];
    private ?MsgSecurity $msgSecurity = null;

    public function test_it_prepare_a_message_to_publish_correctly(): void
    {
        $msgEncode = $this->createMsgSecurity()->prepareMsgToPublish($this->createMsgStructure('user::created'));

        $msgOut = json_decode($msgEncode, true);
        $this->assertIsNotArray($msgOut['body']);
        $this->assertArrayHasKey('signature', $msgOut);
    }

    private function createMsgStructure(string $event, ?array $body = null): MessageStructure
    {
        $body = $body ?: $this->msgBody;
        return new MessageStructure(1, $event, $body);
    }

    private function createMsgSecurity(): MsgSecurity
    {
        if ($this->msgSecurity) {
            return $this->msgSecurity;
        }
        return $this->msgSecurity = new MsgSecurity(
            $this->encryptSecretKey,
            $this->encryptMethod,
            $this->encryptAlgorithm,
            $this->signerAlgorithm,
            file_get_contents(__DIR__ . '/Utils/Signature/publicKey.pem'),
            file_get_contents(__DIR__ . '/Utils/Signature/privateKey.pem')
        );
    }
}
