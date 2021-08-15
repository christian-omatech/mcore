<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules;

use Exception;
use Omatech\Mcore\Editora\Domain\Value\Metadata;

final class LookupValueOptionException extends Exception
{
    public static function withAttributeLanguage(Metadata $metadata): self
    {
        throw new self("Lookup option value does not
        exist for the attribute {$metadata->attributeKey()} in language {$metadata->language()}.");
    }
}
