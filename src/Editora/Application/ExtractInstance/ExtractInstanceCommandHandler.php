<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Contracts\ExtractionCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extraction;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Instance as ExtractionInstance;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Query;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\RelationsResults;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Results;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class ExtractInstanceCommandHandler
{
    private ExtractionRepositoryInterface $extractionRepository;
    private ExtractionCacheInterface $extractionCache;

    public function __construct(
        ExtractionRepositoryInterface $extractionRepository,
        ExtractionCacheInterface $extractionCache
    ) {
        $this->extractionRepository = $extractionRepository;
        $this->extractionCache = $extractionCache;
    }

    public function __invoke(ExtractInstanceCommand $command): Extraction
    {
        $extraction = new Extraction($command->query());
        return $this->extractionCache->get($extraction->hash()) ?? $this->extract($extraction);
    }

    private function extract(Extraction $extraction): Extraction
    {
        $extraction->setQueries(map(function (Query $query): Query {
            $results = $this->extractionRepository->instancesBy($query->params());
            return $query
                ->setPagination($results->pagination())
                ->setResults($this->extractResults($query, $results->instances()));
        }, (new QueryParser())->parse($extraction->query())));
        $this->extractionCache->put($extraction->hash(), $extraction);
        return $extraction;
    }

    /**
     * @param Query $query
     * @param array<int, Instance> $instances
     *
     * @return array<int, ExtractionInstance>
     */
    private function extractResults(Query $query, array $instances): array
    {
        return map(function (Instance $instance) use ($query): ExtractionInstance {
            $relations = $this->findRelatedInstances($query->relations(), $instance);
            return (new Extractor($query, $instance, $relations))->extract();
        }, $instances);
    }

    private function findRelatedInstances(array $relations, Instance $instance): array
    {
        return reduce(function (array $acc, Query $query) use ($instance): array {
            $results = $this->extractionRepository->findRelatedInstances(
                $instance->uuid(),
                $query->params()
            );
            $query->setPagination($results->pagination());
            $acc[] = (new RelationsResults($query->params()))
                ->setResults($results)
                ->setRelations($this->fillRelations($results, $query));
            return $acc;
        }, $relations, []);
    }

    private function fillRelations(Results $results, Query $query): array
    {
        return flat_map(function (Instance $instance) use ($query) {
            if ($instance->relations()->count()) {
                return $this->findRelatedInstances($query->relations(), $instance);
            }
            return [];
        }, $results->instances());
    }
}
