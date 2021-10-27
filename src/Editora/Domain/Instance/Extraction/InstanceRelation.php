<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

final class InstanceRelation
{
    private string $key;
    private string $type;
    private Results $results;
    private array $relations;

    public function __construct(array $params)
    {
        $this->key = $params['class'];
        $this->type = $params['type'];
    }

    public function setResults(Results $results): InstanceRelation
    {
        $this->results = $results;
        return $this;
    }

    public function setRelations(array $relations): InstanceRelation
    {
        $this->relations = $relations;
        return $this;
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
        return $this->results->instances();
    }

    public function relations(): array
    {
        return $this->relations;
    }
}
