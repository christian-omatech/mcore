<?php declare(strict_types=1);

namespace Omatech\Mcore\Shared\Domain\Event\Contracts;

use Omatech\Mcore\Shared\Domain\Event\Event;

interface EventPublisherInterface
{
    /** @param array<Event> $events */
    public function publish(array $events): void;
}
