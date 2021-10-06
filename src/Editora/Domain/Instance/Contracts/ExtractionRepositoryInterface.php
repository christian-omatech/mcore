<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Contracts;

use Omatech\Mcore\Editora\Domain\Instance\Extraction\Results;

interface ExtractionRepositoryInterface
{
    public function instancesBy(array $params): Results;
    public function findRelatedInstances(int $instanceId, array $params): Results;
}
