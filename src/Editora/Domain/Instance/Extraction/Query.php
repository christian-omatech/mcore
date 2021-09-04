<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use Omatech\Mcore\Editora\Domain\Instance\Instance;
use function DeepCopy\deep_copy;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Query
{
    private string $key;
    private string $language;
    private array $attributes;
    private array $params;
    private array $relations;

    public function __construct(array $query)
    {
        $this->key = $query['key'];
        $this->language = $query['params']['language'] ?? '';
        $this->attributes = $query['attributes'];
        $this->params = $query['params'];
        $this->relations = $query['relations'];
    }

    public function addRelations(array $instanceRelations): void
    {
        $this->relations = $this->matchRelationsInstances($instanceRelations, $this->relations);
    }

    private function matchRelationsInstances(array $instanceRelations, array $queryRelations)
    {
        return reduce(function ($acc, $relations, $key) use ($queryRelations): array {
            $acc[] = $this->addToQueryRelations($relations, $key, $queryRelations);
            return $acc;
        }, $instanceRelations, []);
    }

    private function addToQueryRelations(array $relations, string $key, array $queryRelations)
    {
        $relation = search(static fn ($relation) => $relation->key() === $key, $queryRelations);
        $instances = reduce(function (
            array $acc,
            Instance $instance
        ) use ($relations, $relation): array {
            if ($instance->relations()->count()) {
                $relations = $this->matchRelationsInstances(
                    $relations['relations'],
                    $relation->relations()
                );
            } else {
                $relations = [];
            }
            $acc[] = $this->newQuery($instance, $relation, $relations);
            return $acc;
        }, $relations['instances'], []);
        $relation->setInstances($instances);
        return $relation;
    }

    private function newQuery(Instance $instance, Relation $relation, array $relations): Query
    {
        return new Query([
            'key' => $instance->key(),
            'attributes' => map(static function (Attribute $attribute) {
                return deep_copy($attribute);
            }, $relation->attributes()),
            'params' => $this->params,
            'relations' => $relations,
        ]);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function relations(): array
    {
        return $this->relations;
    }

    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'language' => $this->language,
            'attributes' => map(
                static fn (Attribute $attribute) => $attribute->toArray(),
                $this->attributes
            ),
            'params' => $this->params,
            'relations' => reduce(static function (
                array $acc,
                array $relations,
                string $key
            ): array {
                $acc[$key] = map(static fn (Query $query) => $query->toArray(), $relations);
                return $acc;
            }, $this->relations, []),
        ];
    }
}
