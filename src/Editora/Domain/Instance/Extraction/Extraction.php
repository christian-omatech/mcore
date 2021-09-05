<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\map;

final class Extraction
{
    /** @var array<Query> $queries */
    private array $queries;
    /** @var array<Instance> $instances */
    private array $instances;

    public function __construct(array $queries, array $instances)
    {
        $this->queries = $queries;
        $this->instances = $instances;
    }

    public function queries(): array
    {
        return $this->queries;
    }

    public function instances(): array
    {
        return $this->instances;
    }

    public function toArray(): ?array
    {
        $instances = map(static fn (Instance $instance) => $instance->toArray(), $this->instances);
        return count($instances) < 2 ? first($instances) : $instances;
    }
}
