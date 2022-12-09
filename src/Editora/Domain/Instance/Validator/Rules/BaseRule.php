<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Rules;

use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;

abstract class BaseRule
{
    protected AttributeCollection $attributeCollection;
    protected mixed $conditions;

    public function __construct(
        AttributeCollection $attributeCollection,
        mixed $conditions
    ) {
        $this->attributeCollection = $attributeCollection;
        $this->conditions = $conditions;
    }

    abstract public function validate(BaseValue $value): void;
}
