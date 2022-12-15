<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final readonly class Pagination
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

    public function limit(): int
    {
        return $this->limit ?: $this->total;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit();
    }

    private function pages(): int
    {
        return $this->limit ? (int) ceil($this->total / $this->limit()) : 1;
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
