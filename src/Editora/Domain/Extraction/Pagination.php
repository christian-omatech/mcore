<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final readonly class Pagination
{
    private int $limit;
    private int $page;

    public function __construct(array $params, private int $total)
    {
        $this->limit = $params['limit'] > 0 ? $params['limit'] : $total;
        $this->page = $params['page'];
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    private function pages(): int
    {
        return (int) ceil($this->total / $this->limit);
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
