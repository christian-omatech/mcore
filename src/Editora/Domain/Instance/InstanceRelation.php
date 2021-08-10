<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

final class InstanceRelation
{
    private string $key;
    private string $class;
    /** @var array<int> $instanceIds */
    private array $instanceIds;

    public function __construct(string $key, string $class, array $instanceIds)
    {
        $this->key = $key;
        $this->class = $class;
        $this->instanceIds = $instanceIds;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function clazz(): string
    {
        return $this->class;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'class' => $this->class,
            'instanceIds' => $this->instanceIds,
        ];
    }
}
