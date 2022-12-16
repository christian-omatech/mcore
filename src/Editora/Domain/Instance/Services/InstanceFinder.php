<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Services;

use Omatech\MageCore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InstanceExistsException;
use Omatech\MageCore\Editora\Domain\Instance\Instance;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

class InstanceFinder
{
    public function __construct(private readonly InstanceRepositoryInterface $repository)
    {
    }

    public function findOrFail(string $uuid): Instance
    {
        $instance = $this->repository->find($uuid);
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
        return map(fn (array $relationKey): array => reduce(function (?array $acc, string $uuid): array {
            $acc[$uuid] = $this->repository->classKey($uuid) ??
                throw new InstanceDoesNotExistsException();
            return $acc;
        }, $relationKey, []), $relations);
    }
}
