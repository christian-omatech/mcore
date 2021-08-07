<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

use function Lambdish\Phunctional\map;

class RelationCollection
{
    /** @var array<Relation> $relations */
    private array $relations;

    public function __construct(array $relations)
    {
        $this->relations = map(static function (array $relation): Relation {
            return new Relation($relation['key'], $relation['classes']);
        }, $relations);
    }

    public function toArray(): array
    {
        return map(static fn (Relation $relation) => $relation->toArray(), $this->relations);
    }
}
