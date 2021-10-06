<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Clazz;

use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

final class RelationCollection
{
    /** @var array<Relation> $relations */
    private array $relations;

    public function __construct(array $relations)
    {
        $this->relations = flat_map(static function (array $classes, $key): Relation {
            return new Relation($key, $classes);
        }, $relations);
    }

    /** @return array<Relation> */
    public function get(): array
    {
        return $this->relations;
    }

    public function toArray(): array
    {
        return map(static fn (Relation $relation) => $relation->toArray(), $this->relations);
    }
}
