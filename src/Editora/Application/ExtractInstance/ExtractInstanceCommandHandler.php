<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Instance as ExtractionInstance;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Query;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\reduce;

final class ExtractInstanceCommandHandler
{
    private InstanceRepositoryInterface $instanceRepository;
    private InstanceFinder $instanceFinder;

    public function __construct(InstanceRepositoryInterface $instanceRepository)
    {
        $this->instanceRepository = $instanceRepository;
        $this->instanceFinder = new InstanceFinder($instanceRepository);
    }

    public function __invoke(ExtractInstanceCommand $command): ExtractionInstance | array
    {
        $queries = (new QueryParser())->parse($command->query());
        $instances = reduce(function (array $acc, Query $query) {
            $instance = $this->instanceRepository->findByKey($query->key());
            $relations = $this->prepareRelations($query->relations(), $instance);
            $acc[] = (new Extractor($query, $instance, $relations))->extract();
            return $acc;
        }, $queries, []);
        if (count($queries) === 1) {
            return first($instances);
        }
        return $instances;
    }

    private function prepareRelations(array $relations, Instance $instance)
    {
        return reduce(function (array $acc, Query $query) use ($instance) {
            $instances = $this->instanceRepository->findChildrenInstances(
                $instance->id(),
                $query->key(),
                $query->params()
            );
            $acc[$query->key()]['instances'] = $instances;
            $acc[$query->key()]['relations'] = $this->fillRelations($instances, $query);
            return $acc;
        }, $relations, []);
    }

    private function fillRelations(array $instances, Query $query)
    {
        return reduce(function (array $acc, Instance $instance) use ($query) {
            if ($instance->relations()->count()) {
                $acc = $this->prepareRelations($query->relations(), $instance);
            }
            return $acc;
        }, $instances, []);
    }
}
