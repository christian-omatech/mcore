<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value\Exceptions;

use Exception;

final class InvalidRuleException extends Exception
{
    public static function withRule(string $rule): InvalidRuleException
    {
        throw new self("The property ${rule} do not exists.");
    }
}
