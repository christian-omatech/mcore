<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance;

use Omatech\Ecore\Editora\Domain\Attribute\AttributeCollection;

abstract class Instance
{
    private Metadata $metadata;
    private Publication $publication;
    private AttributeCollection $attributesCollection;

    public function __construct(array $instance)
    {
        $this->metadata = new Metadata($instance['metadata']);
        $this->publication = new Publication();
        $this->attributesCollection = new AttributeCollection($instance['attributes']);
    }

    public function fill(array $instance): void
    {
        $this->metadata->fill($instance['metadata']);
        $this->publication->fill($instance['publication']);
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
            'metadata' => $this->metadata->toArray(),
            'publication' => $this->publication->toArray(),
            'attributes' => $this->attributesCollection->get(),
        ];
    }
}
