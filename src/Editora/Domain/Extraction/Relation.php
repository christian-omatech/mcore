<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Extraction;

final class Relation
{
    private string $key;
    private string $type;
    /** @var array<Instance> $instances */
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
