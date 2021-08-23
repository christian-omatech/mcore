<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Validator\Rules;

use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\RequiredValueException;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use Omatech\Mcore\Shared\Utils\Utils;

final class Required extends BaseRule
{
    public function validate(BaseValue $value): void
    {
        if ($this->conditions === true && Utils::getInstance()->isEmpty($value->value())) {
            RequiredValueException::withValue($value);
        }
    }
}
