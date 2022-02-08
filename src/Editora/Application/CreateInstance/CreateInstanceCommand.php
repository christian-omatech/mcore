<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Application\CreateInstance;

use Omatech\Mcore\Shared\Application\Command;

final class CreateInstanceCommand extends Command
{
    /**
     * @var string
     */
    private string $classKey;

    /**
     * @var array{
     *     uuid: string,
     *     key: string,
     *     publication: array{
     *         status: string,
     *         startPublishingDate: string,
     *         endPublishingDate: string
     *     }
     * }
     */
    private array $metadata;

    /**
     * @var array<string, mixed>
     */
    private array $attributes;

    /**
     * @var array<string, mixed>
     */
    private array $relations;

    public function __construct(array $data)
    {
        $this->classKey = $data['classKey'];
        $this->metadata = [
            'uuid' => $data['uuid'],
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

    /**
     * @return array{
     *     uuid: string,
     *     key: string,
     *     publication: array{
     *         status: string,
     *         startPublishingDate: string,
     *         endPublishingDate: string
     *     }
     * }
     */
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
