<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

final class Metadata
{
    private ?string $uuid = null;
    private string $key = '';
    private Publication $publication;

    public function __construct()
    {
        $this->publication = new Publication();
    }

    public function fill(array $metadata): void
    {
        assert(isset($metadata['key']));
        assert(isset($metadata['publication']));
        $this->uuid = $metadata['uuid'] ?? $this->uuid;
        $this->key = $metadata['key'];
        $this->publication->fill($metadata['publication']);
    }

    public function uuid(): ?string
    {
        return $this->uuid;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function data(): array
    {
        return [
            'key' => $this->key,
        ] + $this->publication->data();
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'key' => $this->key,
            'publication' => $this->publication->toArray(),
        ];
    }
}
