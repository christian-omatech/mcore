<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

class RelationCollection
{
    private array $relations;

    public function __construct(array $relations)
    {
        $this->relations = $relations;
    }
}
