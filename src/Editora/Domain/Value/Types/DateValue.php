<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Types;

use Omatech\Mcore\Editora\Domain\Value\BaseValue;

final class DateValue extends BaseValue
{
    public function value(): ?string
    {
        return $this->value;
    }
}
