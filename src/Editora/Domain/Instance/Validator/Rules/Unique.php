<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Validator\Rules;

use Omatech\Mcore\Editora\Domain\Attribute\Attribute;
use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Contracts\UniqueValueInterface;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueException;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\flatten;
use function Lambdish\Phunctional\reduce;

final class Unique extends BaseRule
{
    private UniqueValueInterface $repository;

    public function __construct(AttributeCollection $attributeCollection, mixed $conditions)
    {
        $this->repository = new $conditions['class']();
        parent::__construct($attributeCollection, $conditions);
    }

    public function validate(BaseValue $value): void
    {
        $this->validateInInstance($value);
        $this->validateInDB($value);
    }

    private function validateInInstance(BaseValue $value): void
    {
        $results = filter(static function (BaseValue $current) use ($value): bool {
            return $current->value() === $value->value() &&
                $current->value() !== null &&
                $value->value() !== null;
        }, $this->attributesValues($value->key()));
        if (count($results) > 1) {
            UniqueValueException::withValue($value);
        }
    }

    private function attributesValues(string $key): array
    {
        $attributes = $this->attributeCollection->findAll($key);
        return flatten(reduce(static function (array $acc, Attribute $attribute): array {
            $acc[] = $attribute->values()->get();
            return $acc;
        }, $attributes, []));
    }

    private function validateInDB(BaseValue $value): void
    {
        if (! $this->repository->isUnique($value)) {
            UniqueValueException::withValue($value);
        }
    }
}
