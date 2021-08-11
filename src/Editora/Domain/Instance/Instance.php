<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Clazz\Clazz;

abstract class Instance
{
    private Clazz $clazz;
    private Metadata $metadata;
    private AttributeCollection $attributesCollection;
    private InstanceRelationCollection $instanceRelationCollection;

    public function __construct(array $instance)
    {
        $this->clazz = new Clazz($instance['metadata']);
        $this->metadata = new Metadata();
        $this->attributesCollection = new AttributeCollection($instance['attributes']);
        $this->instanceRelationCollection = new InstanceRelationCollection();
    }

    public function fill(array $instance): void
    {
        assert(isset($instance['metadata']));
        assert(isset($instance['attributes']));
        assert(isset($instance['relations']));
        $this->metadata->fill($instance['metadata']);
        $this->attributesCollection->fill($instance['attributes']);
        $this->instanceRelationCollection->fill($instance['relations']);
        $this->validate();
    }

    private function validate(): void
    {
        $this->attributesCollection->validate();
        $this->clazz->validateRelations($this->instanceRelationCollection->instanceRelations());
    }

    public function toArray(): array
    {
        return [
            'class' => $this->clazz->toArray(),
            'metadata' => $this->metadata->toArray(),
            'attributes' => $this->attributesCollection->get(),
            'relations' => $this->instanceRelationCollection->get(),
        ];
    }
}
