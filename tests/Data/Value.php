<?php declare(strict_types=1);

namespace Tests\Data;

use Omatech\Ecore\Editora\Domain\Value\BaseValue;

class Value extends BaseValue
{
    public function value(): mixed
    {
        return $this->value;
    }
}
