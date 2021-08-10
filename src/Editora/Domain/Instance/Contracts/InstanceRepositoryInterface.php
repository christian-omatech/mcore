<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance\Contracts;

use Omatech\Ecore\Editora\Domain\Instance\Instance;

interface InstanceRepositoryInterface
{
    public function build(string $classKey): Instance;
    public function find(int $id): ?Instance;
    public function delete(Instance $instance): void;
    public function save(Instance $instance): void;
}
