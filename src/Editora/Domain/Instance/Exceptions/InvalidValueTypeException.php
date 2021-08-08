<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance\Exceptions;

use Exception;

final class InvalidValueTypeException extends Exception
{
    public static function withType(string $type): self
    {
        throw new self("${type} do not exists.");
    }
}
