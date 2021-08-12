<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Attribute;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;
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

    public function validate(): void
    {
        each(static fn (Attribute $attribute) => $attribute->validate(), $this->attributes);
    }

    public function get(): array
    {
        return map(static fn (Attribute $attribute) => $attribute->toArray(), $this->attributes);
    }
}
