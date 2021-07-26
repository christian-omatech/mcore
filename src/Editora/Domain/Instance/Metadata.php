<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance;

final class Metadata
{
    private string $className;
    private string $caption;
    private ?int $id = null;
    private ?string $key = null;
    private array $allowedRelations;

    public function __construct(array $metadata)
    {
        $this->className = $metadata['className'];
        $this->caption = $metadata['caption'];
        $this->allowedRelations = $metadata['relations'];
    }

    public function fill(array $metadata): void
    {
        $this->id = $metadata['id'] ?? null;
        $this->key = $metadata['key'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'className' => $this->className,
            'caption' => $this->caption,
            'id' => $this->id,
            'key' => $this->key,
            'allowedRelations' => $this->allowedRelations,
        ];
    }
}
