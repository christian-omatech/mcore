<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Attribute;

use Omatech\Ecore\Editora\Domain\Value\ValueCollection;

final class Attribute
{
    private Metadata $metadata;
    private Component $component;
    private ValueCollection $valueCollection;
    private AttributeCollection $attributeCollection;

    public function __construct(array $properties)
    {
        $this->metadata = new Metadata($properties['key']);
        $this->component = new Component($properties['type'], $properties['caption']);
        $this->valueCollection = new ValueCollection($properties['values']);
        $this->attributeCollection = new AttributeCollection($properties['attributes']);
    }

    public function fill(array $values): void
    {
        $this->valueCollection->fill($values['values']);
        $this->attributeCollection->fill($values['attributes'] ?? []);
    }

    public function validate(): void
    {
        $this->valueCollection->validate($this->key());
        $this->attributeCollection->validate();
    }

    public function key(): string
    {
        return $this->metadata->key();
    }

    public function toArray(): array
    {
        return [
            'metadata' => $this->metadata->toArray(),
            'component' => $this->component->toArray(),
            'values' => $this->valueCollection->get(),
            'attributes' => $this->attributeCollection->get(),
        ];
    }
}
