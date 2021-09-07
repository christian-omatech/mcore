<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Events;

use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Shared\Domain\Event\Event;

final class InstanceHasBeenUpdated extends Event
{
    private Instance $old;
    private Instance $new;

    public function __construct(Instance $old, Instance $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    public function old(): Instance
    {
        return $this->old;
    }

    public function new(): Instance
    {
        return $this->new;
    }
}
