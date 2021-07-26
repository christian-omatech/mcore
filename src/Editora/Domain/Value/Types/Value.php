<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value\Types;

use Omatech\Ecore\Editora\Domain\Value\BaseValue;

final class Value extends BaseValue
{
    public function value(): mixed
    {
        return $this->value;
    }
}
