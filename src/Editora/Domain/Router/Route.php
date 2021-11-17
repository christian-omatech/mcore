<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Router;

final class Route
{
    private string $uri;
    private string $schema;
    private string $namespace;

    public function __construct(array $route)
    {
        $this->schema = $route['schema'] ?? $route['uri'];
        $this->uri = $route['uri'];
        $this->namespace = $route['namespace'];
    }

    public function extraction(string $class): string
    {
        $this->ensureClassIsAllowed($class);
        return $this->namespace . '\\' . ucfirst($class) . 'Controller';
    }

    private function ensureClassIsAllowed(string $class): void
    {
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function schema(): string
    {
        return $this->schema;
    }
}
