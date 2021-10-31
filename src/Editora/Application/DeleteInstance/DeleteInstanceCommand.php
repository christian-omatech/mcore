<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\DeleteInstance;

use Omatech\Mcore\Shared\Application\Command;

final class DeleteInstanceCommand extends Command
{
    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }
}
