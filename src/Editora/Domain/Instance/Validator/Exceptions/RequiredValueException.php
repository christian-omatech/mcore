<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions;

use Exception;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;

final class RequiredValueException extends Exception
{
    public static function withValue(BaseValue $value): self
    {
        throw new self("The value is required for the attribute {$value->key()}
            in language {$value->language()}.");
    }
}
