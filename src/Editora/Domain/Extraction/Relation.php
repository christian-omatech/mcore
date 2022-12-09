<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final class Relation
{
    private readonly string $key;
    private readonly string $type;
    private array $instances = [];

    public function __construct(string $key, string $type)
    {
        $this->key = $key;
        $this->type = $type;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function instances(): array
    {
        return $this->instances;
    }

    public function setInstances(array $instances): Relation
    {
        $this->instances = $instances;
        return $this;
    }
}
