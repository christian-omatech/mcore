<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

final class Metadata
{
    private ?int $id = null;
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
        $this->id = $metadata['id'] ?? $this->id;
        $this->key = $metadata['key'];
        $this->publication->fill($metadata['publication']);
    }

    public function id(): ?int
    {
        return $this->id;
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
            'id' => $this->id,
            'key' => $this->key,
            'publication' => $this->publication->toArray(),
        ];
    }
}
