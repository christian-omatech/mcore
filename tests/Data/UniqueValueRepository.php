<?php
namespace Tests\Data;

use Omatech\Mcore\Editora\Domain\Value\Rules\Contracts\UniqueValueInterface;

final class UniqueValueRepository implements UniqueValueInterface
{
    public function unique(string $key, mixed $value): bool
    {
        return true;
    }
}
