<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class Instance
{
    private readonly string $key;
    private readonly array $attributes;
    private readonly array $relations;

    public function __construct(array $query)
    {
        $this->key = $query['key'];
        $this->attributes = $query['attributes'];
        $this->relations = $query['relations'];
    }

    private function relatedInstancesToArray(array $relations): array
    {
        return reduce(function (array $acc, Relation $relation): array {
            $acc[$relation->key()][$relation->type()] =
                $this->instancesToArray($relation->instances());
            return $acc;
        }, $relations, []);
    }

    private function instancesToArray(array $instances): array
    {
        return map(static fn (Instance $instance): array => $instance->toArray(), $instances);
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'attributes' => reduce(static function (
                array $acc,
                array $attributes,
                string $language
            ): array {
                $acc[$language] = map(static fn (Attribute $attribute): array => $attribute->toArray(), $attributes);
                return $acc;
            }, $this->attributes, []),
            'relations' => $this->relatedInstancesToArray($this->relations),
        ];
    }
}
