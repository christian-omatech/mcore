<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Clazz\Exceptions;

use Exception;

final class InvalidRelationClassException extends Exception
{
    public static function withRelationClasses(string $key, array $classes): self
    {
        $classes = implode(', ', $classes);
        throw new self("Classes {$classes} are not valid for relation {$key}");
    }
}
