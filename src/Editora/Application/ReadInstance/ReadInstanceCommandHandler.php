<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ReadInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;

final class ReadInstanceCommandHandler
{
    private InstanceFinder $instanceFinder;

    public function __construct(InstanceRepositoryInterface $instanceRepository)
    {
        $this->instanceFinder = new InstanceFinder($instanceRepository);
    }

    public function __invoke(ReadInstanceCommand $command): array
    {
        $instance = $this->instanceFinder->findOrFail($command->id());
        return $instance->toArray();
    }
}
