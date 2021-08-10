<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ReadInstance;

final class ReadInstanceCommand
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
