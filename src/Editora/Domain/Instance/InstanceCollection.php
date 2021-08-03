<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance;

class InstanceCollection
{
    private array $relations;

    public function __construct(array $relations)
    {
        $this->relations = $relations;
    }

    public function relations(): array
    {
        return $this->relations;
    }
}
