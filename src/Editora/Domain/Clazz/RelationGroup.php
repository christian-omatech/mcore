<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

use function Lambdish\Phunctional\map;

class RelationGroup
{
    private string $key;
    /** @var array<Relation> $relations */
    private array $relations;

    public function __construct(string $key, array $relations)
    {
        $this->key = $key;
        $this->relations = map(static fn (string $classKey) => new Relation($classKey), $relations);
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'relations' => map(static function (Relation $relation) {
                return $relation->classKey();
            }, $this->relations),
        ];
    }
}
