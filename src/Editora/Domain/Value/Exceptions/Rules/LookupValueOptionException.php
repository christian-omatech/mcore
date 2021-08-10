<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules;

use Exception;

final class LookupValueOptionException extends Exception
{
    public static function withAttributeLanguage(string $key, string $language): self
    {
        throw new self("Lookup option value does not 
        exist for the attribute ${key} in language ${language}.");
    }
}
