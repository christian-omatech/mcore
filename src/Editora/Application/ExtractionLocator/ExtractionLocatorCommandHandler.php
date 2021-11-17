<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractionLocator;

use Omatech\Mcore\Editora\Domain\Attribute\Contracts\AttributeRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Router\Router;

final class ExtractionLocatorCommandHandler
{
    private AttributeRepositoryInterface $attributeRepository;

    public function __construct(AttributeRepositoryInterface $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    public function __invoke(ExtractionLocatorCommand $command): array
    {
        $related = $this->attributeRepository->classKeyWithAlternateNiceUrls($command->niceUrl());
        $router = Router::instance($command->router(), $command->languages());
        $extractionLocation = $router->locateExtraction($command->uri(), $related['key']);
        $alternativeUris = $router->alternateUris(
            $command->uri(),
            $command->path(),
            $related['niceUrls']
        );
        return [$extractionLocation, $alternativeUris];
    }
}
