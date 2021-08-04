<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value\Types;

use Omatech\Ecore\Editora\Domain\Value\BaseValue;

final class Date extends BaseValue
{
    public function value(): ?string
    {
        return $this->value;
    }
}
