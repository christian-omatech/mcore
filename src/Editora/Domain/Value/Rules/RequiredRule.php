<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Rules;

use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\RequiredValueException;
use Omatech\Mcore\Shared\Utils\Utils;

final class RequiredRule extends Rule
{
    public function validate(string $key, string $language, mixed $value): void
    {
        if ($this->condition === true && Utils::getInstance()->isEmpty($value)) {
            RequiredValueException::withAttributeLanguage($key, $language);
        }
    }
}
