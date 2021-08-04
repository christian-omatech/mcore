<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

class Relation
{
    private string $classKey;

    public function __construct(string $classKey)
    {
        $this->classKey = $classKey;
    }

    public function classKey(): string
    {
        return $this->classKey;
    }
}
