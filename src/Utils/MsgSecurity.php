<?php

namespace E4\Messaging\Utils;

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
     * @throws Exception
     */
    public function prepareMsgToPublish(MessageStructure $msgStructure): string
    {
        $msg = $msgStructure->jsonSerialize();

        $data = json_encode($msg['body']);
        $msg['signature'] = $this->signature->sign($data);
        $msg['body'] = $this->encryption->encrypt($data);

        return json_encode($msg);
    }

    /* TODO
    public function prepareMsgToRecive(string $msg): MessageStructure
    {
        //Desencriptar
        //VerificarFirma
    }
    */
}
