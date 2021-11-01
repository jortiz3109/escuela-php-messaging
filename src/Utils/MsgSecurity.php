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

    /**
     * @throws Signature\Exceptions\SignatureException
     */
    public function prepareMsgToPublish(MessageStructure $msgStructure): string
    {
        $msg = $msgStructure->jsonSerialize();
        $data = json_encode($msg['body']);
        $msg['body'] = $this->encryption->encrypt($data);
        $msg['signature'] = $this->signature->sign($msg['body']);

        return json_encode($msg);
    }

    /**
     * @throws Signature\Exceptions\SignatureException
     * @throws SignatureVerifyException
     */
    /**
     * @param string $message
     * @return MessageStructure
     * @throws SignatureVerifyException
     * @throws Signature\Exceptions\SignatureException
     */
    public function prepareMsgToReceive(string $message): MessageStructure
    {
        $jsonMessage = json_decode($message);
        if ($this->signature->verify($jsonMessage->body, $jsonMessage->signature)) {
            return new MessageStructure(
                $jsonMessage->event,
                json_decode($this->encryption->decrypt($jsonMessage->body), true),
                $jsonMessage->id
            );
        }
        throw new SignatureVerifyException('Error in message wrong signature');
    }
}
