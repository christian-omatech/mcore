<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Attribute;

use Omatech\MageCore\Editora\Domain\Value\BaseValue;
use Omatech\MageCore\Editora\Domain\Value\ValueCollection;

final class Attribute
{
    private readonly string $key;
    private readonly string $type;
    private readonly ValueCollection $valueCollection;
    private readonly AttributeCollection $attributeCollection;

    public function __construct(array $properties)
    {
        $this->key = $properties['key'];
        $this->type = $properties['type'];
        $this->valueCollection = new ValueCollection($properties['values']);
        $this->attributeCollection = new AttributeCollection($properties['attributes']);
    }

    public function fill(array $values): void
    {
        $this->valueCollection->fill($values['values']);
        $this->attributeCollection->fill($values['attributes'] ?? []);
    }

    public function attributes(): AttributeCollection
    {
        return $this->attributeCollection;
    }

    public function values(): ValueCollection
    {
        return $this->valueCollection;
    }

    public function value(string $language): ?BaseValue
    {
        return $this->valueCollection->language($language);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'type' => $this->type,
            'values' => $this->valueCollection->toArray(),
            'attributes' => $this->attributeCollection->toArray(),
        ];
    }
}
