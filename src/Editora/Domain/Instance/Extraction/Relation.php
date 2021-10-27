<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

final class Relation
{
    private string $name;
    private string $type;
    private Results $results;
    private array $relations;

    public function __construct(array $params)
    {
        $this->name = $params['class'];
        $this->type = $params['type'];
    }

    public function setResults(Results $results): Relation
    {
        $this->results = $results;
        return $this;
    }

    public function setRelations(array $relations): Relation
    {
        $this->relations = $relations;
        return $this;
    }

    public function name(): string
    {
        return $this->name;
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
