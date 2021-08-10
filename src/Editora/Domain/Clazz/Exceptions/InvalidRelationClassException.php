<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Clazz\Exceptions;

use Exception;

final class InvalidRelationClassException extends Exception
{
    public static function withRelationClasses(string $key, string $class): self
    {
        throw new self("Class {$class} is not valid for relation {$key}");
    }
}
