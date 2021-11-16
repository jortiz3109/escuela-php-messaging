<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default App Connection Name
    |--------------------------------------------------------------------------
    */
    'default' => env('PIGEON_CONNECTION', 'rabbitmq'),

    /*
    |--------------------------------------------------------------------------
    | Pigeon Apps Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'ssl' => env('PIGEON_RABBITMQ_SSL', false),
            'host' => [
                'host' => env('PIGEON_RABBITMQ_HOST', '127.0.0.1'),
                'port' => env('PIGEON_RABBITMQ_PORT', '5672'),
                'username' => env('PIGEON_RABBITMQ_USERNAME', 'guest'),
                'password' => env('PIGEON_RABBITMQ_PASSWORD', 'guest'),
                'vhost' => env('PIGEON_RABBITMQ_VHOST', '/'),
            ],
            'exchange' => [
                'name' => env('PIGEON_RABBITMQ_EXCHANGE_NAME', 'direct_exchange'),
                'type' => env('PIGEON_RABBITMQ_EXCHANGE_TYPE', 'direct'),
            ],
            'queue' => [
                'name' => env('PIGEON_RABBITMQ_QUEUE_NAME', 'queue'),
            ],
            'ssl_options' => [
                'cafile' => env('PIGEON_RABBITMQ_SSL_CAFILE', null),
                'local_cert' => env('PIGEON_RABBITMQ_SSL_LOCAL_CERT', null),
                'local_key' => env('PIGEON_RABBITMQ_SSL_LOCAL_KEY', null),
                'verify_peer' => env('PIGEON_RABBITMQ_SSL_VERIFY_PEER', true),
                'passphrase' => env('PIGEON_RABBITMQ_SSL_PASSPHRASE', null),
            ],
            'consumer_wait' => env('PIGEON_RABBITMQ_CONSUMER_WAIT_SECONDS', 3),
        ],
    ],
    'signature' => [
        'algorithm' => env('PIGEON_SIGNATURE_ALGORITHM', OPENSSL_ALGO_SHA256),
        'publicKey' => env('PIGEON_SIGNATURE_PUBLICKEY', 'public_key'),
        'privateKey' => env('PIGEON_SIGNATURE_PRIVATEKEY', null),
    ],
    'encryption' => [
        'secretKey' => env('PIGEON_ENCRYPT_SECRETKEY', 'CLASS-MESSAGE-KEY'),
        'method' => env('PIGEON_ENCRYPT_METHOD', 'AES-256-CBC'),
        'algorithm' => env('PIGEON_ENCRYPT_ALGORITHM', 'sha256'),
    ],
    'events' => [

    ],
];
