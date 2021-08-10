<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value\Types;

use Omatech\Ecore\Editora\Domain\Value\Exceptions\Rules\LookupValueOptionException;
use Omatech\Ecore\Shared\Utils\Utils;

final class LookupValue extends StringValue
{
    public function validate(): void
    {
        parent::validate();
        $this->ensureLookupIsValid();
    }

    public function value(): ?string
    {
        return $this->value;
    }

    private function ensureLookupIsValid(): void
    {
        if (! Utils::getInstance()->isEmpty($this->value) &&
            ! in_array($this->value, $this->configuration()['options'])) {
            LookupValueOptionException::withAttributeLanguage($this->key(), $this->language());
        }
    }
}
