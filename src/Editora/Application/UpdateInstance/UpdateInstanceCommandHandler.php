<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\UpdateInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Events\InstanceHasBeenUpdated;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;
use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;

final class UpdateInstanceCommandHandler
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

    public function __invoke(UpdateInstanceCommand $command): void
    {
        $relations = $this->instanceFinder->findClassKeysGivenInstances($command->relations());
        $instance = $this->instanceFinder->findOrFail($command->uuid());
        $old = $this->instanceRepository->clone($instance);
        $instance->fill([
            'metadata' => $command->metadata(),
            'attributes' => $command->attributes(),
            'relations' => $relations,
        ]);
        $this->instanceRepository->save($instance);
        $this->eventPublisher->publish([new InstanceHasBeenUpdated($old, $instance)]);
    }
}
