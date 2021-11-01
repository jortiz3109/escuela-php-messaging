<?php

namespace E4\Messaging\Utils;

use JsonSerializable;

class MessageStructure implements JsonSerializable
{
    public string $id;
    public string $event;
    public array $body;

    public function __construct(string $event, array $body, string $id = '')
    {
        $this->id = $id;
        $this->event = $event;
        $this->body = $body;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'event' => $this->event,
            'body' => $this->body,
        ];
    }
}
