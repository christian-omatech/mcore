<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\search;

final class ValueCollection
{
    /** @var array<BaseValue> */
    private array $values;

    /** @param array<BaseValue> $values */
    public function __construct(array $values)
    {
        $this->values = map(static fn (BaseValue $value) => $value, $values);
    }

    /** @param array<BaseValue> $values */
    public function fill(array $values): void
    {
        each(function (mixed $value, string $language): void {
            search(static function (BaseValue $value) use ($language): bool {
                return $value->language() === $language;
            }, $this->values)?->fill($value);
        }, $values);
    }

    public function validate(string $key): void
    {
        each(static fn (BaseValue $value) => $value->validate($key), $this->values);
    }

    /** @return array<BaseValue> */
    public function get(): array
    {
        return map(static fn (BaseValue $value) => $value->toArray(), $this->values);
    }
}
