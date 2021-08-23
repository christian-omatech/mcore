<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Types;

use Omatech\Mcore\Editora\Domain\Value\Exceptions\LookupValueOptionException;
use Omatech\Mcore\Shared\Utils\Utils;

final class LookupValue extends StringValue
{
    public function fill(array $value): void
    {
        $this->ensureLookupIsValid($value['value']);
        parent::fill($value);
    }

    private function ensureLookupIsValid(mixed $value): void
    {
        if (! Utils::getInstance()->isEmpty($value) &&
            ! $this->configuration->exists($value, ['options'])) {
            LookupValueOptionException::withValue($this);
        }
    }
}
