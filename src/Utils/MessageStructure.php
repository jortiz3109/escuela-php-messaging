<?php

namespace E4\Pigeon\Utils;

use JsonSerializable;

class MessageStructure implements JsonSerializable
{
    public string $id;
    public array $body;

    public function __construct(array $body, string $id = '')
    {
        $this->id = $id;
        $this->body = $body;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
        ];
    }
}
