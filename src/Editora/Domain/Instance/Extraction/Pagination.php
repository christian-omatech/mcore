<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

final class Pagination
{
    private int $total;
    private int $limit;
    private int $page;

    public function __construct(array $params, int $total)
    {
        $this->total = $total;
        $this->limit = $params['limit'];
        $this->page = $params['page'];
    }

    public function realLimit(): int
    {
        return $this->limit ? $this->limit : $this->total;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->realLimit();
    }

    private function pages(): int
    {
        return $this->limit ? (int) ceil($this->total / $this->realLimit()) : 1;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'limit' => $this->limit,
            'current' => $this->page,
            'pages' => $this->pages(),
        ];
    }
}
