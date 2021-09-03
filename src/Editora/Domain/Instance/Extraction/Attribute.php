<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use function Lambdish\Phunctional\map;

final class Attribute
{
    private string $key;
    private mixed $value;
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

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'attributes' => map(
                static fn (Attribute $attribute) => $attribute->toArray(),
                $this->attributes
            ),
        ];
    }
}
