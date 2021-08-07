<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

class Relation
{
    private string $key;
    private array $classes;

    public function __construct(string $key, array $classes)
    {
        $this->key = $key;
        $this->classes = $classes;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'classes' => $this->classes,
        ];
    }
}
