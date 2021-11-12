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
    private $publicKey = __DIR__ . '/../certs/signaturePublicKey.pem';
    private $privateKey = __DIR__ . '/../certs/signaturePrivateKey.pem';
    private array $msgBody = [
        'user' => [
            'uuid' => 123,
            'name' => 'Zara Isabell Valencia',
        ],
    ];
    private ?MsgSecurity $msgSecurity = null;

    public function test_it_prepare_a_message_to_publish_correctly(): void
    {
        $msgEncode = $this->createEncodeMessage($this->msgBody);
        $msgOut = json_decode($msgEncode, true);
        $this->assertIsNotArray($msgOut['body']);
        $this->assertArrayHasKey('signature', $msgOut);
    }

    public function test_it_prepare_a_message_to_receive_correctly(): void
    {
        $messageEncode = $this->createEncodeMessage($this->msgBody);
        $msgOut = $this->createMsgSecurity()->prepareMsgToReceive($messageEncode);
        $this->assertEquals($this->msgBody, $msgOut->body);
    }

    public function test_throw_signature_verify(): void
    {
        $this->expectException(SignatureVerifyException::class);
        $messageEncode = json_decode($this->createEncodeMessage($this->msgBody));
        $messageEncode->signature = 'MEUCIQDEjlRMiAYyV0AsT0E9xtN7g2wZeWQO/mrfU5R85uEs6gIgN9/4dfpq4QG7kaOJ9s9Cpm74njKdJPB/O3MKeQgp0QIsasda=';
        $this->createMsgSecurity()->prepareMsgToReceive(json_encode($messageEncode));
    }

    private function createEncodeMessage(?array $body = null): string
    {
        return $this->createMsgSecurity()->prepareMsgToPublish(new MessageStructure($body, 1));
    }

    private function createMsgSecurity(): MsgSecurity
    {
        if (!$this->msgSecurity) {
            $this->msgSecurity = new MsgSecurity(
                $this->encryptSecretKey,
                $this->encryptMethod,
                $this->encryptAlgorithm,
                $this->signerAlgorithm,
                file_get_contents($this->publicKey),
                file_get_contents($this->privateKey)
            );
        }
        return $this->msgSecurity;
    }
}
