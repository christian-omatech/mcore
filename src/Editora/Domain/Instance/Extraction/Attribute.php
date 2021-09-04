<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use function Lambdish\Phunctional\map;

final class Attribute
{
    private ?int $id = null;
    private string $key;
    private mixed $value = null;
    /** @var array<Attribute> $attributes */
    private array $attributes;

    public function __construct(string $key, array $attributes)
    {
        $this->key = $key;
        $this->attributes = $attributes;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function setValue(?int $id, mixed $value): void
    {
        $this->id = $id;
        $this->value = $value;
    }

    public function toQuery(): array
    {
        return [
            'key' => $this->key,
            'attributes' => map(
                static fn (Attribute $attribute) => $attribute->toQuery(),
                $this->attributes
            ),
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'attributes' => map(
                static fn (Attribute $attribute) => $attribute->toArray(),
                $this->attributes
            ),
        ];
    }
}
