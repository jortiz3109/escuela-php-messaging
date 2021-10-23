<?php

namespace E4\Messaging\Utils;

use E4\Messaging\Utils\Encryption\Encryption;
use E4\Messaging\Utils\Signature\Signature;
use Exception;

class MsgSecurity
{
    public static array $msgStructure = [
        'envent' => null,
        'payload' => []
    ];
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

    public function prepareMsgToPublic(array $msg): string
    {
        if (!$this->verifyMsgStructure($msg)) {
            throw new Exception('The structure of the data is incorrect');
        }
        $data = json_encode($msg['payload']);
        $msg['signature'] = $this->signer->sign($data);
        $msg['payload'] = $this->encrypter->encrypt($data);

        return json_encode($msg);
    }

    /* TODO
    public function prepareMsgToRecive(string $msg): array
    {
        //Desencriptar
        //VerificarFirma
    }
    */

    private function verifyMsgStructure(array $msg): bool
    {
        return array_diff_key(self::$msgStructure, $msg) == [];
    }
}
