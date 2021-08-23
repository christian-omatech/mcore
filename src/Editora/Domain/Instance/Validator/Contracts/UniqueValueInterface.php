<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Validator\Contracts;

use Omatech\Mcore\Editora\Domain\Value\BaseValue;

interface UniqueValueInterface
{
    public function isUnique(BaseValue $value): bool;
}
