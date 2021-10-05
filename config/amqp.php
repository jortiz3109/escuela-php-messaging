<?php

return [

    'ssl' => env('AMQP_SSL', false),

    'host' => [
        'host' => env('AMQP_HOST', 'beaver.rmq.cloudamqp.com'),
        'port' => env('AMQP_PORT', '5672'),
        'username' => env('AMQP_USERNAME', 'pzrjdqgp'),
        'password' => env('AMQP_PASSWORD', 'RExuiTPwf0ZxH68RLQoqBlodnMheoSfi'),
        'vhost' => env('AMQP_VHOST', 'pzrjdqgp'),
    ],

    'exchange' => [
        'name' => env('AMQP_EXCHANGE_NAME', 'payment_exchange'),
        'type' => env('AMQP_EXCHANGE_TYPE', 'direct')
    ],

    'queue' => [
        'name' => env('AMQP_QUEUE_NAME', 'hello')
    ],

    'ssl_options' => [
        'cafile' => env('AMQP_SSL_CAFILE', null),
        'local_cert' => env('AMQP_SSL_LOCAL_CERT', null),
        'local_key' => env('AMQP_SSL_LOCAL_KEY', null),
        'verify_peer' => env('AMQP_SSL_VERIFY_PEER', true),
        'passphrase' => env('AMQP_SSL_PASSPHRASE', null),
    ],

];
