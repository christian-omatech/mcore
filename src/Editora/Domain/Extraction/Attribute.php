<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\map;

final class Attribute
{
    private ?string $uuid = null;
    private string $key;
    private mixed $value = null;
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

    public function setValue(array $value): void
    {
        $this->uuid = $value['uuid'];
        $this->value = $value['value'];
    }

    public function toQuery(): array
    {
        return [
            'key' => $this->key,
            'attributes' => map(
                static fn (Attribute $attribute): array => $attribute->toQuery(),
                $this->attributes
            ),
        ];
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'key' => $this->key,
            'value' => $this->value,
            'attributes' => map(
                static fn (Attribute $attribute): array => $attribute->toArray(),
                $this->attributes
            ),
        ];
    }
}
