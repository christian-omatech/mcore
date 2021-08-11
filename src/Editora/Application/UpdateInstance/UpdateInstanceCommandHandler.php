<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\UpdateInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;

final class UpdateInstanceCommandHandler
{
    private InstanceRepositoryInterface $instanceRepository;
    private InstanceFinder $instanceFinder;

    public function __construct(InstanceRepositoryInterface $instanceRepository)
    {
        $this->instanceRepository = $instanceRepository;
        $this->instanceFinder = new InstanceFinder($instanceRepository);
    }

    public function __invoke(UpdateInstanceCommand $command): void
    {
        $relations = $this->instanceFinder->findClassKeysGivenInstances($command->relations());
        $instance = $this->instanceFinder->findOrFail($command->id());
        $instance->fill([
            'metadata' => $command->metadata(),
            'attributes' => $command->attributes(),
            'relations' => $relations,
        ]);
        $this->instanceRepository->save($instance);
    }
}
