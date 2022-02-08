<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class Query
{
    /** @var array|mixed */
    private array $params;

    /** @var array<Attribute> */
    private array $attributes;

    /** @var array<Query> $relations */
    private array $relations;

    /** @var Pagination|null */
    private ?Pagination $pagination = null;

    /** @var array<Instance> $results */
    private array $results;

    public function __construct(array $query)
    {
        $this->attributes = $query['attributes'];
        $this->params = $query['params'];
        $this->relations = $query['relations'];
    }

    /** @return array<Attribute> */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /** @return array<Query> */
    public function relations(): array
    {
        return $this->relations;
    }

    public function params(): array
    {
        return $this->params;
    }

    /** @return array<Instance> */
    public function results(): array
    {
        return $this->results;
    }

    /** @param array<Instance> $results */
    public function setResults(array $results): self
    {
        $this->results = $results;
        return $this;
    }

    public function pagination(): Pagination
    {
        return $this->pagination;
    }

    public function setPagination(Pagination $pagination): self
    {
        $this->pagination = $pagination;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'languages' => $this->languages(),
            'attributes' => map(static function (Attribute $attribute): array {
                return $attribute->toQuery();
            }, $this->attributes),
            'params' => $this->params,
            'relations' => reduce(static function (array $acc, Query $query): array {
                $acc[] = $query->toArray();
                return $acc;
            }, $this->relations, []),
            'pagination' => $this->pagination?->toArray(),
        ];
    }

    public function languages(): array
    {
        return $this->param('languages');
    }

    public function param(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }
}
