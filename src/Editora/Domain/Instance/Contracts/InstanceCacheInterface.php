<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Contracts;

use Omatech\Mcore\Editora\Domain\Instance\Instance;

interface InstanceCacheInterface
{
    public function has(string $className): bool;
    public function get(string $className): ?Instance;
    public function put(string $className, Instance $instance): void;
}
