<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extraction;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Query;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class ExtractInstanceCommandHandler
{
    private ExtractionRepositoryInterface $extractionRepository;

    public function __construct(ExtractionRepositoryInterface $extractionRepository)
    {
        $this->extractionRepository = $extractionRepository;
    }

    public function __invoke(ExtractInstanceCommand $command): Extraction
    {
        $extraction = new Extraction($command->query());
        $queries = map(function (Query $query) {
            $instances = $this->extractionRepository->instancesBy($query->params());
            return $query
                ->setPagination($instances['pagination'])
                ->setResults($this->extractResults($query, $instances));
        }, (new QueryParser())->parse($command->query()));
        return $extraction->setQueries($queries);
    }

    private function extractResults(Query $query, array $instances): array
    {
        return map(function (Instance $instance) use ($query) {
            $relations = $this->prepareRelations($query->relations(), $instance);
            return (new Extractor($query, $instance, $relations))->extract();
        }, $instances['instances']);
    }

    private function prepareRelations(array $relations, Instance $instance)
    {
        return reduce(function (array $acc, Query $query) use ($instance) {
            $instances = $this->extractionRepository->findChildrenInstances(
                $instance->id(),
                $query->params()
            );
            $acc[$query->param('class')]['instances'] = $instances;
            $acc[$query->param('class')]['relations'] = $this->fillRelations($instances, $query);
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
