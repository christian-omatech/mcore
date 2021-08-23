<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Attribute;

use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class AttributeCollection
{
    /** @var array<Attribute> $attributes */
    private array $attributes;

    /** @param array<Attribute> $attributes */
    public function __construct(array $attributes)
    {
        $this->attributes = map(static fn (Attribute $attribute) => $attribute, $attributes);
    }

    public function fill(array $attributes): void
    {
        each(function (array $values, string $key): void {
            search(static function (Attribute $attribute) use ($key): bool {
                return $attribute->key() === $key;
            }, $this->attributes)?->fill($values);
        }, $attributes);
    }

    public function find(string $key): array
    {
        return $this->search($this->attributes, $key);
    }

    private function search(array $attributes, string $key): array
    {
        return reduce(function (array $acc, Attribute $attribute) use ($key) {
            $current = [];
            $sub = $this->search($attribute->attributes()->get(), $key);
            if ($attribute->key() === $key) {
                $current = reduce(static function (array $acc, BaseValue $baseValue) {
                    $acc[] = [
                        'value' => $baseValue->value(),
                    ];
                    return $acc;
                }, $attribute->values()->get(), []);
            }
            return array_merge($acc, $current, $sub);
        }, $attributes, []);
    }

    public function get(): array
    {
        return map(static fn (Attribute $attribute) => $attribute, $this->attributes);
    }

    public function toArray(): array
    {
        return map(static fn (Attribute $attribute) => $attribute->toArray(), $this->attributes);
    }
}
