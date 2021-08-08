<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

use Omatech\Ecore\Editora\Domain\Clazz\Exceptions\InvalidRelationClassException;

final class Relation
{
    private string $key;
    private array $classes;

    public function __construct(string $key, array $classes)
    {
        $this->key = $key;
        $this->classes = $classes;
    }

    public function validate(string $class): void
    {
        if (! in_array($class, $this->classes, true)) {
            InvalidRelationClassException::withRelationClasses($this->key, $class);
        }
    }

    public function key(): string
    {
        return $this->key;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'classes' => $this->classes,
        ];
    }
}
