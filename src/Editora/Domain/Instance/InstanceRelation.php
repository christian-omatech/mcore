<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

final class InstanceRelation
{
    private string $key;
    private array $instances;

    public function __construct(string $key, array $instances)
    {
        $this->key = $key;
        $this->instances = $instances;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function classes(): array
    {
        return $this->instances;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'instances' => $this->instances,
        ];
    }
}
