<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\DeleteInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Events\InstanceHasBeenDeleted;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;
use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;

final class DeleteInstanceCommandHandler
{
    private EventPublisherInterface $eventPublisher;
    private InstanceRepositoryInterface $instanceRepository;
    private InstanceFinder $instanceFinder;

    public function __construct(
        EventPublisherInterface $eventPublisher,
        InstanceRepositoryInterface $instanceRepository
    ) {
        $this->eventPublisher = $eventPublisher;
        $this->instanceRepository = $instanceRepository;
        $this->instanceFinder = new InstanceFinder($instanceRepository);
    }

    public function __invoke(DeleteInstanceCommand $command): void
    {
        $instance = $this->instanceFinder->findOrFail($command->id());
        $this->instanceRepository->delete($instance);
        $this->eventPublisher->publish([new InstanceHasBeenDeleted($instance)]);
    }
}
