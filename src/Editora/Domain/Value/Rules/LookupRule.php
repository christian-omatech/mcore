<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value\Rules;

use Omatech\Ecore\Editora\Domain\Value\Exceptions\Rules\LookupValueOptionException;

final class LookupRule extends Rule
{
    public function validate(string $key, string $language, mixed $value): void
    {
        if (! $this->isEmpty($value) && ! in_array($value, $this->condition)) {
            LookupValueOptionException::withAttributeLanguage($key, $language);
        }
    }
}
