<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\search;

final class ValueCollection
{
    /** @var array<BaseValue> */
    private readonly array $values;

    /** @param array<BaseValue> $values */
    public function __construct(array $values)
    {
        $this->values = map(static fn (BaseValue $value) => $value, $values);
    }

    public function fill(array $values): void
    {
        each(function (mixed $value): void {
            search(static fn (BaseValue $fillableValue): bool => $fillableValue->language() === $value['language'], $this->values)?->fill($value);
        }, $values);
    }

    public function language(string $language): ?BaseValue
    {
        return search(static fn (BaseValue $baseValue) => $baseValue->language() === $language, $this->values);
    }

    /** @return array<BaseValue> */
    public function get(): array
    {
        return $this->values;
    }

    public function toArray(): array
    {
        return map(static fn (BaseValue $value) => $value->toArray(), $this->values);
    }
}
