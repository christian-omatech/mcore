<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Query;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Relation;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;
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

    public function __invoke(ExtractInstanceCommand $command): Query
    {
        $query = (new QueryParser())->parse($command->query());
        $instance = $this->instanceRepository->findByKey($query->key());
        $relations = $this->prepareRelations($query->relations(), $instance);

        $query->addRelations($relations);
        $extractor = new Extractor($query, $instance, $relations);
        return $extractor->extract();
    }

    private function prepareRelations(array $relations, Instance $instance)
    {
        return reduce(function (array $acc, Relation $relation) use ($instance) {
            $instances = $this->instanceRepository->findChildrenInstances(
                $instance->id(),
                $relation->key(),
                $relation->params()
            );
            $acc[$relation->key()]['instances'] = $instances;
            $acc[$relation->key()]['relations'] = $this->fillRelations($instances, $relation);
            return $acc;
        }, $relations, []);
    }

    private function fillRelations(array $instances, Relation $relation)
    {
        return reduce(function (array $acc, Instance $instance) use ($relation) {
            if ($instance->relations()->count()) {
                $acc = $this->prepareRelations($relation->relations(), $instance);
            }
            return $acc;
        }, $instances, []);
    }
}