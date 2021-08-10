<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Rules;

abstract class Rule
{
    protected mixed $condition;

    public function __construct(mixed $condition)
    {
        $this->condition = $condition;
    }

    abstract public function validate(string $key, string $language, mixed $value): void;

    public function condition(): mixed
    {
        return $this->condition;
    }
}
