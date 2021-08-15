<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value;

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

    public function fill(array $values): void
    {
        each(function (mixed $value): void {
            search(static function (BaseValue $fillableValue) use ($value): bool {
                return $fillableValue->language() === $value['language'];
            }, $this->values)?->fill($value['value'], $value['id']);
        }, $values);
    }

    public function validate(): void
    {
        each(static fn (BaseValue $value) => $value->validate(), $this->values);
    }

    public function get(): array
    {
        return map(static fn (BaseValue $value) => $value->toArray(), $this->values);
    }
}
