<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

class Clazz
{
    private Metadata $metadata;
    private RelationCollection $relations;

    public function __construct(array $metadata)
    {
        $this->metadata = new Metadata($metadata['name'], $metadata['caption']);
        $this->relations = new RelationCollection($metadata['relations']);
    }

    public function toArray(): array
    {
        return $this->metadata->toArray() + [
            'relations' => [],
        ];
    }
}
