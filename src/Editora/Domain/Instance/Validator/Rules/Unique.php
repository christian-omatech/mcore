<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Rules;

use Omatech\MageCore\Editora\Domain\Attribute\Attribute;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueException;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\flat_map;

class Unique extends BaseRule
{
    public function validate(BaseValue $value): void
    {
        $this->validateInInstance($value);
    }

    private function validateInInstance(BaseValue $value): void
    {
        $results = filter(static fn (BaseValue $current): bool => $current->value() === $value->value() &&
            $current->value() !== null &&
            $value->value() !== null, $this->attributesValues($value->key()));

        if (count($results) > 1) {
            UniqueValueException::withValue($value);
        }
    }

    private function attributesValues(string $key): array
    {
        return flat_map(static fn (Attribute $attribute): array => $attribute->values()->get(), $this->attributeCollection->findAll($key));
    }
}
