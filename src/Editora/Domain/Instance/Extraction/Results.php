<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

final class Results
{
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

    public function toArray(): array
    {
        return [
            'instances' => $this->instances,
            'pagination' => $this->pagination->toArray(),
        ];
    }
}
