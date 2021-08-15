<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Types;

use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\LookupValueOptionException;
use Omatech\Mcore\Shared\Utils\Utils;

final class LookupValue extends StringValue
{
    public function validate(): void
    {
        parent::validate();
        $this->ensureLookupIsValid();
    }

    private function ensureLookupIsValid(): void
    {
        if (! Utils::getInstance()->isEmpty($this->value) &&
            ! $this->configuration->exists($this->value, ['options'])) {
            LookupValueOptionException::withAttributeLanguage($this->metadata);
        }
    }
}
