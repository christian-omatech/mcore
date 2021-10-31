<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Contracts;

use Omatech\Mcore\Editora\Domain\Instance\Instance;

interface InstanceRepositoryInterface
{
    public function build(string $classKey): Instance;
    public function clone(Instance $instance): Instance;
    public function find(string $uuid): ?Instance;
    public function exists(string $key): bool;
    public function classKey(string $uuid): ?string;
    public function delete(Instance $instance): void;
    public function save(Instance $instance): void;
}
