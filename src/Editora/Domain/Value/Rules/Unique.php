<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Rules;

use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\UniqueValueException;
use Omatech\Mcore\Editora\Domain\Value\Rules\Contracts\UniqueValueInterface;

final class Unique extends Rule
{
    private UniqueValueInterface $uniqueValue;

    public function __construct(mixed $condition)
    {
        $this->uniqueValue = new $condition['class']();
        unset($condition['class']);
        parent::__construct($condition);
    }

    public function validate(string $key, string $language, mixed $value): void
    {
        if ($this->uniqueValue->unique($key, $value)) {
            UniqueValueException::withAttribute($key, $language);
        }
    }
}
