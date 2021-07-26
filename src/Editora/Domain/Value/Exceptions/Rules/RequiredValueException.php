<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value\Exceptions\Rules;

use Exception;

final class RequiredValueException extends Exception
{
    public static function withAttributeLanguage(
        string $key,
        string $language
    ): RequiredValueException {
        throw new self("The value is required for the attribute ${key} in language ${language}.");
    }
}
