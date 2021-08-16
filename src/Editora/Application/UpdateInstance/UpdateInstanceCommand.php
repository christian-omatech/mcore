<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\UpdateInstance;

final class UpdateInstanceCommand
{
    private int $id;
    private array $metadata;
    private array $attributes;
    private array $relations;

    public function __construct(array $data)
    {
        $this->id = $data['metadata']['id'];
        $this->metadata = $data['metadata'];
        $this->attributes = $data['attributes'];
        $this->relations = $data['relations'] ?? [];
    }

    public function id(): int
    {
        return $this->id;
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
