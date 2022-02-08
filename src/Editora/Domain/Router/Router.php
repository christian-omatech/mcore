<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Router;

use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Router
{
    private static ?self $instance = null;
    private array $languages;
    private array $routes;

    private function __construct(array $routerConfiguration, array $languages)
    {
        $this->languages = $languages;
        $this->routes = reduce(function (array $acc, array $route): array {
            return array_merge($acc, $this->prepareRoutes($route));
        }, $routerConfiguration, []);
    }

    public static function instance(array $routerConfiguration, array $languages): self
    {
        if (! self::$instance) {
            self::$instance = new self($routerConfiguration, $languages);
        }
        return self::$instance;
    }

    public function prepareRoutes(array $route): array
    {
        if ($route['translate'] ?? false === true) {
            return reduce(function (array $acc, string $language) use ($route): array {
                $route['schema'] = $route['uri'];
                $route['uri'] = $this->translateUri($language, $route['uri']);
                $acc[] = new Route($route);
                return $acc;
            }, $this->languages, []);
        }
        return [new Route($route)];
    }

    private function translateUri(string $language, string $uri): string
    {
        $uri = implode('/', map(static function (string $segment) use ($language): string {
            return preg_replace(
                '/^[^{](.*)[^}]/',
                trans('mage.editora.router.segment.' . $segment, [], $language),
                $segment
            );
        }, explode('/', $uri)));
        return preg_replace('/{language}/', $language, $uri);
    }

    private function searchRoute(string $uri): Route
    {
        return search(static fn (Route $route): bool => $route->uri() === $uri, $this->routes);
    }

    public function locateExtraction(string $uri, string $class): string
    {
        $route = $this->searchRoute($uri);
        return $route->extraction($class);
    }

    public function alternateUris(string $uri, string $path, array $niceUrlLanguages): array
    {
        $route = $this->searchRoute($uri);
        return map(function (string $niceUrl, string $language) use ($route, $path): string {
            $uri = $this->translateUri($language, $route->schema());
            return $this->setVariableSegments(preg_replace('/{niceUrl}/', $niceUrl, $uri), $path);
        }, $niceUrlLanguages);
    }

    private function setVariableSegments(string $uri, string $path): string
    {
        $uriSegments = explode('/', $uri);
        $variableSegments = filter(static function ($segment) {
            return preg_match('/[{](.*)[}]/', $segment);
        }, $uriSegments);
        $variableSegments = reduce(static function ($acc, $index) use ($path) {
            $acc[$index] = explode('/', $path)[$index];
            return $acc;
        }, array_flip($variableSegments), []);
        return implode('/', array_replace($uriSegments, $variableSegments));
    }

    public function routes(): array
    {
        return $this->routes;
    }
}
