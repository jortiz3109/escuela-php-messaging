<?php

namespace E4\Messaging\Utils;

use JsonSerializable;

class MessageStructure implements JsonSerializable
{

    private string $id;
    private string $event;
    private array $body;

    public function __construct(string $id, string $event, array $body)
    {
        $this->id = $id;
        $this->event = $event;
        $this->body = $body;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "event" => $this->event,
            "body" => $this->body
        ];
    }
}
