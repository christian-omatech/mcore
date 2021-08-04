<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

use function Lambdish\Phunctional\map;

class RelationCollection
{
    /** @var array<RelationGroup> $relations */
    private array $relationGroups;

    public function __construct(array $relations)
    {
        $this->relationGroups = map(static function (array $relationGroup) {
            return new RelationGroup($relationGroup['key'], $relationGroup['classes']);
        }, $relations);
    }

    public function toArray(): array
    {
        return map(static function (RelationGroup $relationGroup) {
            return $relationGroup->toArray();
        }, $this->relationGroups);
    }
}
