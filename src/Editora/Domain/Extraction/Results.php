<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final class Results
{
    /** @var array<Instance> $instances */
    private readonly array $instances;
    private readonly ?Pagination $pagination;

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
