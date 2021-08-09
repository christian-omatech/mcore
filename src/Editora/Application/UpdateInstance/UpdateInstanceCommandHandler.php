<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Application\UpdateInstance;

use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Ecore\Editora\Domain\Instance\Services\InstanceFinder;

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
        $instance = $this->instanceFinder->findOrFail($command->id());
        $instance->fill([
            'metadata' => $command->metadata(),
            'attributes' => $command->attributes(),
            'relations' => $command->relations(),
        ]);
        $this->instanceRepository->save($instance);
    }
}
