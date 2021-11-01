<?php

namespace Tests\Unit;

use E4\Messaging\Exceptions\SignatureVerifyException;
use E4\Messaging\Utils\MessageStructure;
use E4\Messaging\Utils\MsgSecurity;
use Tests\TestCase;

class MsgSecurityTest extends TestCase
{
    private int $signerAlgorithm = OPENSSL_ALGO_SHA256;
    private $encryptSecretKey = 'CLASS-MESSAGE-KEY';
    private $encryptMethod = 'AES-256-CBC';
    private $encryptAlgorithm = 'sha256';
    private $publicKey = __DIR__ . '/Utils/Signature/publicKey.pem';
    private $privateKey = __DIR__ . '/Utils/Signature/privateKey.pem';
    private array $msgBody = [
        'user' => [
            'uuid' => 123,
            'name' => "Esperanza Gomez"
        ]
    ];
    private ?MsgSecurity $msgSecurity = null;

    public function test_it_prepare_a_message_to_publish_correctly(): string
    {
        $msgEncode = $this->createEncodeMessage('user::created');

        $msgOut = json_decode($msgEncode, true);
        $this->assertIsNotArray($msgOut['body']);
        $this->assertArrayHasKey('signature', $msgOut);
        return $msgEncode;
    }


    public function test_it_prepare_a_message_to_receive_correctly(): void
    {
        $messageEncode = $this->createEncodeMessage('message::receive', $this->msgBody);
        $msgOut = $this->createMsgSecurity()->prepareMsgToReceive($messageEncode);
        $this->assertEquals('message::receive', $msgOut->event);
        $this->assertEquals($this->msgBody, $msgOut->body);
    }

    public function test_throw_signature_verify(): void
    {
        $this->expectException(SignatureVerifyException::class);
        $messageEncode = json_decode($this->createEncodeMessage('message::bad_sig', $this->msgBody));
        $messageEncode->signature = 'MEUCIQDEjlRMiAYyV0AsT0E9xtN7g2wZeWQO/mrfU5R85uEs6gIgN9/4dfpq4QG7kaOJ9s9Cpm74njKdJPB/O3MKeQgp0QI=';
        $this->createMsgSecurity()->prepareMsgToReceive(json_encode($messageEncode));
    }

    private function createEncodeMessage(string $event, ?array $body = null): string
    {
        return $this->createMsgSecurity()->prepareMsgToPublish($this->createMsgStructure($event, $body));
    }

    private function createMsgStructure(string $event, ?array $body = null): MessageStructure
    {
        $body = $body ?: $this->msgBody;
        return new MessageStructure($event, $body, 1);
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
            file_get_contents($this->publicKey),
            file_get_contents($this->privateKey)
        );
    }
}
