<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

final class ExtractInstanceCommand
{
    private string $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }
}
