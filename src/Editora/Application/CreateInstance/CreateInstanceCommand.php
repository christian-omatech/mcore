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
        $this->metadata = $data['metadata'];
        $this->attributes = $data['attributes'];
        $this->relations = $data['relations'] ?? [];
    }

    public function classKey(): string
    {
        return $this->classKey;
    }

    public function metadata(): array
    {
        $startPublishingDate = $this->metadata['publication']['startPublishingDate'];
        $endPublishingDate = $this->metadata['publication']['endPublishingDate'] ?? null;
        return [
            'key' => $this->metadata['key'],
            'publication' => [
                'startPublishingDate' => $startPublishingDate,
                'endPublishingDate' => $endPublishingDate,
            ],
        ];
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
