<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use DateTime;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class Extraction
{
    private readonly string $query;
    private readonly string $hash;
    private readonly DateTime $date;
    private array $queries;

    public function __construct(string $query)
    {
        $this->query = $query;
        $this->hash = md5($query);
        $this->date = new DateTime();
    }

    public function query(): string
    {
        return $this->query;
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function date(): DateTime
    {
        return $this->date;
    }

    public function queries(): array
    {
        return $this->queries;
    }

    public function setQueries(array $queries): void
    {
        $this->queries = $queries;
    }

    public function toArray(): array
    {
        $results = reduce(static function (array $acc, Query $query): array {
            $instances = map(
                static fn (Instance $instance): array => $instance->toArray(),
                $query->results()
            );
            $acc[] = count($instances) < 2 ? first($instances) ?? [] : $instances;
            return $acc;
        }, $this->queries, []);
        return (is_countable($results) ? count($results) : 0) < 2 ? first($results) ?? [] : $results;
    }
}
