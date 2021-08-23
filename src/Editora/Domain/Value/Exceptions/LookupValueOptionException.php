<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Exceptions;

use Exception;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;

final class LookupValueOptionException extends Exception
{
    public static function withValue(BaseValue $value): self
    {
        throw new self("Lookup option value does not
        exist for the attribute {$value->key()} in language {$value->language()}.");
    }
}
