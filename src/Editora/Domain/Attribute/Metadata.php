<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Attribute;

final class Metadata
{
    private string $key;
    private ?int $id = null;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
        ];
    }

    public function key(): string
    {
        return $this->key;
    }
}
