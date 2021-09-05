<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\UpdateInstance;

use Omatech\Mcore\Shared\Application\Command;

final class UpdateInstanceCommand extends Command
{
    private int $id;
    private array $metadata;
    private array $attributes;
    private array $relations;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
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
