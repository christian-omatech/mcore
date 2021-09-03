<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

final class Relation
{
    private string $key;
    private array $params;
    private array $attributes;
    private array $relations;
    private array $instances = [];

    public function __construct(string $key, array $params, array $attributes, array $relations)
    {
        $this->key = $key;
        $this->params = $params;
        $this->attributes = $attributes;
        $this->relations = $relations;
    }

    public function instances(): array
    {
        return $this->instances;
    }

    public function setInstances(array $instances): void
    {
        $this->instances = $instances;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function relations(): array
    {
        return $this->relations;
    }

    public function params(): array
    {
        return $this->params;
    }
}
