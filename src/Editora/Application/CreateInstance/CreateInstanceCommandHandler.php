<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\CreateInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;

final class CreateInstanceCommandHandler
{
    private InstanceRepositoryInterface $instanceRepository;

    public function __construct(InstanceRepositoryInterface $instanceRepository)
    {
        $this->instanceRepository = $instanceRepository;
    }

    public function __invoke(CreateInstanceCommand $command): void
    {
        $instance = $this->instanceRepository->build($command->classKey());
        $instance->fill([
            'metadata' => $command->metadata(),
            'attributes' => $command->attributes(),
            'relations' => $command->relations(),
        ]);
        $this->instanceRepository->save($instance);
    }
}
