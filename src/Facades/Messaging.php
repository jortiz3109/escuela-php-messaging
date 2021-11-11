<?php

namespace E4\Messaging\Facades;

use E4\Messaging\MessageBroker;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void publish(string $routingKey, string $event, array $body, string $id = null, array $properties = [])
 *
 * @see MessageBroker
 */
class Messaging extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MessageBroker::class;
    }
}
