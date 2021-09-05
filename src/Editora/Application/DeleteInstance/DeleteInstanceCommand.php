<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\DeleteInstance;

use Omatech\Mcore\Shared\Application\Command;

final class DeleteInstanceCommand extends Command
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
