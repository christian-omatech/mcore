<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

use Omatech\Mcore\Shared\Application\Command;

final class ExtractInstanceCommand extends Command
{
    private string $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public function query(): string
    {
        return $this->query;
    }
}
