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
        $this->id = $data['id'];
        $this->metadata = [
            'publication' => [
                'startPublishingDate' => $data['startPublishingDate'],
                'endPublishingDate' => $data['endPublishingDate'] ?? null,
            ],
        ];
        $this->attributes = $data['attributes'] ?? [];
        $this->relations = $data['relations'] ?? [];
    }

    public function id(): int
    {
        return $this->id;
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
