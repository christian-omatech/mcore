<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Contracts;

interface ExtractionRepositoryInterface
{
    public function instanceByKey(array $params): array;
    public function instancesByClass(array $params): array;
    public function findChildrenInstances(int $instanceId, string $key, array $params): array;
}
