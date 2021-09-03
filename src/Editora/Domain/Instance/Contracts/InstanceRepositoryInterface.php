<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Contracts;

use Omatech\Mcore\Editora\Domain\Instance\Instance;

interface InstanceRepositoryInterface
{
    public function build(string $classKey): Instance;
    public function find(int $id): ?Instance;
    public function findByKey(string $key): ?Instance;
    public function findChildrenInstances(int $instanceId, string $key, array $params): array;
    public function exists(string $key): bool;
    public function classKey(int $id): ?string;
    public function delete(Instance $instance): void;
    public function save(Instance $instance): void;
}
