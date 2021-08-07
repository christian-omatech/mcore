<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance;

use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;

class InstanceCache implements InstanceCacheInterface
{
    private static ?InstanceCache $instance = null;
    private ?array $cache = [];

    public static function getInstance(): InstanceCache
    {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function has(string $className): bool
    {
        return array_key_exists($className, $this->cache);
    }

    public function get(string $className): ?Instance
    {
        return $this->has($className) ? clone $this->cache[$className] : null;
    }

    public function put(string $className, Instance $instance): void
    {
        $this->cache[$className] = $instance;
    }
}
