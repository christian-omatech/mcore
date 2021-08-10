<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

final class InstanceRelationCollection
{
    /** @var array<InstanceRelation> $instanceRelations */
    private array $instanceRelations = [];

    public function fill(array $relations): void
    {
        $this->instanceRelations = flat_map(static function (array $classes, string $relationKey) {
            return map(static function (array $instanceIds, string $classKey) use ($relationKey) {
                return new InstanceRelation($relationKey, $classKey, $instanceIds);
            }, $classes);
        }, $relations);
    }

    public function instanceRelations(): array
    {
        return $this->instanceRelations;
    }

    public function get(): array
    {
        return map(static function (InstanceRelation $relation): array {
            return $relation->toArray();
        }, $this->instanceRelations);
    }
}
