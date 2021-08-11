<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Clazz;

use Omatech\Mcore\Editora\Domain\Clazz\Exceptions\InvalidRelationException;
use Omatech\Mcore\Editora\Domain\Instance\InstanceRelation;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\search;

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

    public function validate(array $instanceRelations): void
    {
        each(function (InstanceRelation $instanceRelation): void {
            $relation = search(static function (Relation $relation) use ($instanceRelation): bool {
                return $relation->key() === $instanceRelation->key();
            }, $this->relations);
            if (is_null($relation)) {
                InvalidRelationException::withRelation($instanceRelation->key());
            }
            $relation->validate($instanceRelation->classes());
        }, $instanceRelations);
    }

    public function toArray(): array
    {
        return map(static fn (Relation $relation) => $relation->toArray(), $this->relations);
    }
}
