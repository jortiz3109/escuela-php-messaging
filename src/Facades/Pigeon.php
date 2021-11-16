<?php

namespace E4\Pigeon\Facades;

use E4\Pigeon\MessageBroker;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void publish(string $event, array $body, string $id = null, array $properties = [])
 *
 * @see MessageBroker
 */
class Pigeon extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MessageBroker::class;
    }
}
