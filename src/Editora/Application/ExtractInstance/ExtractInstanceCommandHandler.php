<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extraction;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Query;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Results;
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
            $results = $this->extractionRepository->instancesBy($query->params());
            return $query
                ->setPagination($results->pagination())
                ->setResults($this->extractResults($query, $results->instances()));
        }, (new QueryParser())->parse($command->query()));
        return $extraction->setQueries($queries);
    }

    private function extractResults(Query $query, array $instances): array
    {
        return map(function (Instance $instance) use ($query) {
            $relations = $this->prepareRelations($query->relations(), $instance);
            return (new Extractor($query, $instance, $relations))->extract();
        }, $instances);
    }

    private function prepareRelations(array $relations, Instance $instance)
    {
        return reduce(function (array $acc, Query $query) use ($instance) {
            $results = $this->extractionRepository->findChildrenInstances(
                $instance->id(),
                $query->params()
            );
            $query->setPagination($results->pagination());
            $acc[$query->param('class')]['instances'] = $results;
            $acc[$query->param('class')]['relations'] = $this->fillRelations($results, $query);
            return $acc;
        }, $relations, []);
    }

    private function fillRelations(Results $results, Query $query)
    {
        return reduce(function (array $acc, Instance $instance) use ($query) {
            if ($instance->relations()->count()) {
                $acc = $this->prepareRelations($query->relations(), $instance);
            }
            return $acc;
        }, $results->instances(), []);
    }
}
