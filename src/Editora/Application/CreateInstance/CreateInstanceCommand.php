<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\CreateInstance;

final class CreateInstanceCommand
{
    private string $classKey;
    private array $metadata;
    private array $attributes;
    private array $relations;

    public function __construct(array $data)
    {
        $this->classKey = $data['classKey'];
        $this->metadata = [
            'key' => $data['key'],
            'publication' => [
                'status' => $data['status'] ?? null,
                'startPublishingDate' => $data['startPublishingDate'],
                'endPublishingDate' => $data['endPublishingDate'] ?? null,
            ],
        ];
        $this->attributes = $data['attributes'] ?? [];
        $this->relations = $data['relations'] ?? [];
    }

    public function classKey(): string
    {
        return $this->classKey;
    }

    public function key(): string
    {
        return $this->metadata['key'];
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function relations(): array
    {
        return $this->relations;
    }
}
