<?php

namespace E4\Messaging\Utils;

use E4\Messaging\Exceptions\SignatureVerifyException;
use E4\Messaging\Utils\Encryption\Encryption;
use E4\Messaging\Utils\Signature\Signature;

class MsgSecurity
{
    private Signature $signature;
    private Encryption $encryption;

    public function __construct(
        string $encryptSecretKey,
        string $encryptMethod,
        string $encryptAlgorithm,
        int $signatureAlgorithm,
        string $signaturePublicKey,
        ?string $signaturePrivateKey = null
    ) {
        $this->encryption = new Encryption($encryptSecretKey, $encryptMethod, $encryptAlgorithm);
        $this->signature = new Signature($signatureAlgorithm, $signaturePublicKey, $signaturePrivateKey);
    }

    public function prepareMsgToPublish(MessageStructure $msgStructure): string
    {
        $msg = $msgStructure->jsonSerialize();
        $data = json_encode($msg['body']);
        $msg['body'] = $this->encryption->encrypt($data);
        $msg['signature'] = $this->signature->sign($msg['body']);

        return json_encode($msg);
    }

    /**
     * @throws SignatureVerifyException
     */
    /**
     * @param string $message
     * @return MessageStructure
     * @throws SignatureVerifyException
     * @throws \Exception
     */
    public function prepareMsgToReceive(string $message): MessageStructure
    {
        $jsonMessage = json_decode($message);

        if (!isset($jsonMessage->body) && !isset($jsonMessage->signature)) {
            throw new \Exception('Does not have a well-defined message structure');
        }

        if (!$this->signature->verify($jsonMessage->body, $jsonMessage->signature)) {
            throw new \Exception('Its not possible to verify the message');
        }

        $bodyDecrypt = json_decode($this->encryption->decrypt($jsonMessage->body), true);
        if (!$bodyDecrypt) {
            throw new \Exception('Its not possible to decrypt the message');
        }
        return new MessageStructure(
            $bodyDecrypt,
            $jsonMessage->id
        );
    }
}
