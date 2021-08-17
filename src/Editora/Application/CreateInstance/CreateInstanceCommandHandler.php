<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\CreateInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;

final class CreateInstanceCommandHandler
{
    private InstanceRepositoryInterface $instanceRepository;
    private InstanceFinder $instanceFinder;

    public function __construct(InstanceRepositoryInterface $instanceRepository)
    {
        $this->instanceRepository = $instanceRepository;
        $this->instanceFinder = new InstanceFinder($instanceRepository);
    }

    public function __invoke(CreateInstanceCommand $command): void
    {
        $this->instanceFinder->exists($command->key());
        $relations = $this->instanceFinder->findClassKeysGivenInstances($command->relations());
        $instance = $this->instanceRepository->build($command->classKey());
        $instance->fill([
            'metadata' => $command->metadata(),
            'attributes' => $command->attributes(),
            'relations' => $relations,
        ]);
        $this->instanceRepository->save($instance);
    }
}
