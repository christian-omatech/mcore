<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Rules\Contracts;

interface UniqueValueInterface
{
    public function unique(string $key, mixed $value): bool;
}
