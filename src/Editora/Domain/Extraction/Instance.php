<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class Instance
{
    private string $key;
    private array $attributes;
    private array $relations;

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
        return map(static function (Instance $instance): array {
            return $instance->toArray();
        }, $instances);
    }

    /**
     * @return array{key: string, attributes: array, relations: array}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'attributes' => reduce(static function (
                array $acc,
                array $attributes,
                string $language
            ): array {
                $acc[$language] = map(static function (Attribute $attribute): array {
                    return $attribute->toArray();
                }, $attributes);
                return $acc;
            }, $this->attributes, []),
            'relations' => $this->relatedInstancesToArray($this->relations),
        ];
    }
}
