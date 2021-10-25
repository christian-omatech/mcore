<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use function Lambdish\Phunctional\first;
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
        return reduce(function (array $acc, array $relations, string $key): array {
            $acc[$key] = $this->instancesToArray($relations);
            if (count($acc[$key]) === 1) {
                $acc[$key] = first($acc[$key]);
                return $acc;
            }
            return $acc;
        }, $relations, []);
    }

    private function instancesToArray(array $relations): array
    {
        return map(static function (array $instances): array {
            return map(static function (Instance $instance): array {
                return $instance->toArray();
            }, $instances);
        }, $relations);
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'attributes' => map(static function (Attribute $attribute): array {
                return $attribute->toArray();
            }, $this->attributes),
            'relations' => $this->relatedInstancesToArray($this->relations),
        ];
    }
}
