<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Contracts;

interface ExtractionRepositoryInterface
{
    public function instancesBy(array $params): array;
    public function findChildrenInstances(int $instanceId, array $params): array;
}
