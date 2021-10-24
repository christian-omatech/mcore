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

    private function relationsToArray(array $relations)
    {
        return reduce(static function (array $acc, array $relations, string $key): array {
            $acc[$key] = map(static function (array $instances) {
                return map(static fn (Instance $instance) => $instance->toArray(), $instances);
            }, $relations);
            if (count($acc[$key]) === 1) {
                $acc[$key] = first($acc[$key]);
                return $acc;
            }
            return $acc;
        }, $relations, []);
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'attributes' => map(
                static fn (Attribute $attribute) => $attribute->toArray(),
                $this->attributes
            ),
            'relations' => $this->relationsToArray($this->relations),
        ];
    }
}
