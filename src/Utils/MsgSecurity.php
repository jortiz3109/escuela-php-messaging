<?php

namespace E4\Messaging\Utils;

use E4\Messaging\Utils\Encryption\Encryption;
use E4\Messaging\Utils\Signature\Signature;
use Exception;

class MsgSecurity
{
    private Signature $signer;
    private Encryption $encrypter;

    public function __construct(
        string $encryptSecretKey,
        string $encryptMethod,
        string $encryptAlgorithm,
        int $signatureAlgorithm,
        string $signaturePublicKey,
        ?string $signaturePrivateKey = null
    ) {
        $this->encrypter = new Encryption($encryptSecretKey, $encryptMethod, $encryptAlgorithm);
        $this->signer = new Signature($signatureAlgorithm, $signaturePublicKey, $signaturePrivateKey);
    }

    /**
     * @throws Exception
     */
    public function prepareMsgToPublish(MessageStructure $msgStructure): string
    {
        $msg = $msgStructure->jsonSerialize();
        $this->verifyMsgStructure($msg);

        $data = json_encode($msg['body']);
        $msg['signature'] = $this->signer->sign($data);
        $msg['body'] = $this->encrypter->encrypt($data);

        return json_encode($msg);
    }

    /* TODO
    public function prepareMsgToRecive(string $msg): array
    {
        //Desencriptar
        //VerificarFirma
    }
    */

    /**
     * @throws Exception
     */
    private function verifyMsgStructure(array $msg): void
    {
        if (empty($msg['event'])) {
            throw new Exception('The "event" attribute cannot be empty');
        }
    }
}
