<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value\Rules;

use Omatech\Ecore\Editora\Domain\Value\Exceptions\Rules\RequiredValueException;

final class RequiredRule extends Rule
{
    public function validate(string $key, string $language, mixed $value): void
    {
        if ($this->condition === true && $value === null) {
            RequiredValueException::withAttributeLanguage($key, $language);
        }
    }
}
