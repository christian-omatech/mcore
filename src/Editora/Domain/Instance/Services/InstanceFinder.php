<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Services;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

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

    public function exists(string $key): void
    {
        if ($this->repository->exists($key)) {
            throw new InstanceExistsException();
        }
    }

    public function findClassKeysGivenInstances(array $relations): array
    {
        return map(function (array $relationKey): array {
            return reduce(function (?array $acc, int $id): array {
                $classKey = $this->repository->classKey($id) ??
                    throw new InstanceDoesNotExistsException();
                return ($acc ?? []) + [$id => $classKey];
            }, $relationKey);
        }, $relations);
    }
}
