<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ReadInstance;

use Omatech\Mcore\Shared\Application\Command;

final class ReadInstanceCommand extends Command
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function id(): int
    {
        return $this->id;
    }
}
