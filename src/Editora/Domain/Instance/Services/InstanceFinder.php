<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Services;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;

final class InstanceFinder
{
    private InstanceRepositoryInterface $repository;

    public function __construct(InstanceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findOrFail(int $id): Instance
    {
        $instance = $this->repository->find($id);
        return $instance ?? throw new InstanceDoesNotExistsException();
    }
}
