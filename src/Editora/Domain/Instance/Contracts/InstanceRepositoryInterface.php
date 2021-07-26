<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance\Contracts;

use Omatech\Ecore\Editora\Application\CreateInstance\CreateInstanceCommand;
use Omatech\Ecore\Editora\Domain\Instance\Instance;

interface InstanceRepositoryInterface
{
    public function create(CreateInstanceCommand $command): Instance;
    public function save(Instance $instance): void;
}
