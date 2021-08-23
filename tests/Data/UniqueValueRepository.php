<?php declare(strict_types=1);

namespace Tests\Data;

use Omatech\Mcore\Editora\Domain\Instance\Validator\Contracts\UniqueValueInterface;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;

final class UniqueValueRepository implements UniqueValueInterface
{
    public function isUnique(BaseValue $baseValue): bool
    {
        if ($baseValue->id() === null && $baseValue->key() === 'default-attribute4') {
            return false;
        }
        return true;
    }
}
