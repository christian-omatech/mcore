<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Extraction;

final class Results
{
    /** @var array<Instance> $instances */
    private array $instances;
    private ?Pagination $pagination;

    public function __construct(array $instances, ?Pagination $pagination)
    {
        $this->pagination = $pagination;
        $this->instances = $instances;
    }

    public function instances(): array
    {
        return $this->instances;
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }
}
