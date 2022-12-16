<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final readonly class Results
{
    /**
     * @param array<\Omatech\MageCore\Editora\Domain\Extraction\Instance> $instances
     */
    public function __construct(private array $instances, private ?Pagination $pagination)
    {
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
