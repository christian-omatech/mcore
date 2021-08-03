<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance;

use Omatech\Ecore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Ecore\Editora\Domain\Clazz\Clazz;

abstract class Instance
{
    private Clazz $clazz;
    private Metadata $metadata;
    private AttributeCollection $attributesCollection;
    private InstanceCollection $instanceCollection;

    public function __construct(array $instance)
    {
        $this->clazz = new Clazz($instance['metadata']);
        $this->metadata = new Metadata();
        $this->attributesCollection = new AttributeCollection($instance['attributes']);
    }

    public function fill(array $instance): void
    {
        $this->metadata->fill($instance['metadata']);
        $this->attributesCollection->fill($instance['attributes']);
        $this->validate();
    }

    private function validate(): void
    {
        $this->attributesCollection->validate();
    }

    public function toArray(): array
    {
        return [
            'metadata' => $this->clazz->toArray() + $this->metadata->toArray(),
            'attributes' => $this->attributesCollection->get(),
        ];
    }
}
