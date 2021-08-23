<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Validator\Rules;

use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Contracts\UniqueValueInterface;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueException;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use function Lambdish\Phunctional\filter;

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
        $values = $this->attributeCollection->find($value->key());
        $results = filter(static function ($current) use ($value) {
            return $current['value'] === $value->value() &&
                $current['value'] !== null &&
                $value->value() !== null;
        }, $values);
        if (count($results) > 1) {
            UniqueValueException::withValue($value);
        }
    }

    private function validateInDB(BaseValue $value): void
    {
        if (! $this->repository->isUnique($value)) {
            UniqueValueException::withValue($value);
        }
    }
}
