<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Application\CreateInstance;

use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;

final class CreateInstanceCommandHandler
{
    private InstanceRepositoryInterface $instanceRepository;

    public function __construct(
        InstanceRepositoryInterface $instanceRepository
    ) {
        $this->instanceRepository = $instanceRepository;
    }

    public function __invoke(CreateInstanceCommand $command): void
    {
        $instance = $this->instanceRepository->create($command);
        $this->instanceRepository->save($instance);
    }
}
