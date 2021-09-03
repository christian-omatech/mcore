<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractInstance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Query;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Services\InstanceFinder;

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
        $extractor = new Extractor($query, $instance, []);
        return $extractor->extract();
    }
}
