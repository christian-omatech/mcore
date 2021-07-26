<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Application\CreateInstance;

final class CreateInstanceCommand
{
    private string $status;
    private string $className;
    private string $key;
    private array $attributes;

    public function __construct(array $data)
    {
        $this->status = $data['status'];
        $this->className = $data['className'];
        $this->key = $data['key'];
        $this->attributes = $data['attributes'];
    }

    public function status(): string
    {
        return $this->status;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }
}
