<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\ExtractionLocator;

use Omatech\Mcore\Shared\Application\Command;

final class ExtractionLocatorCommand extends Command
{
    private array $languages;
    private array $router;
    private string $niceUrl;
    private string $uri;
    private string $path;

    public function __construct(array $data)
    {
        $this->languages = $data['languages'];
        $this->router = $data['router'];
        $this->niceUrl = $data['niceUrl'];
        $this->uri = $data['uri'];
        $this->path = $data['path'];
    }

    public function languages(): array
    {
        return $this->languages;
    }

    public function router(): array
    {
        return $this->router;
    }

    public function niceUrl(): string
    {
        return $this->niceUrl;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        return $this->path;
    }
}
