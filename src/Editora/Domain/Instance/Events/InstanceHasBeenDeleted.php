<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Events;

use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Shared\Domain\Event\Event;

final class InstanceHasBeenDeleted extends Event
{
    private Instance $instance;

    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    public function instance(): Instance
    {
        return $this->instance;
    }
}
