<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules;

use Exception;

final class UniqueValueException extends Exception
{
    public static function withAttribute(string $key, string $language): self
    {
        throw new self("The value must be unique for the attribute ${key} 
            in language ${language}.");
    }
}
