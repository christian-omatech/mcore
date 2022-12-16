<?php

namespace Omatech\MageCore\Editora\Domain\Extraction;

final readonly class Value
{
    public function __construct(private ?string $uuid, private mixed $value)
    {

    }

    public function uuid(): ?string
    {
        return $this->uuid;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}