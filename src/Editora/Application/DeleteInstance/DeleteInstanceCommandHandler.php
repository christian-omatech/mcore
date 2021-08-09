<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Application\DeleteInstance;

use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Ecore\Editora\Domain\Instance\Services\InstanceFinder;

final class DeleteInstanceCommandHandler
{
    private InstanceRepositoryInterface $instanceRepository;
    private InstanceFinder $instanceFinder;

    public function __construct(InstanceRepositoryInterface $instanceRepository)
    {
        $this->instanceRepository = $instanceRepository;
        $this->instanceFinder = new InstanceFinder($instanceRepository);
    }

    public function __invoke(DeleteInstanceCommand $command): void
    {
        $instance = $this->instanceFinder->findOrFail($command->id());
        $this->instanceRepository->delete($instance);
    }
}
