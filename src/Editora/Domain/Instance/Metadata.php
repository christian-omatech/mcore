<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

final class Metadata
{
    private ?int $id = null;
    private ?string $key = null;
    private Publication $publication;

    public function __construct()
    {
        $this->publication = new Publication();
    }

    public function fill(array $metadata): void
    {
        assert(isset($metadata['key']));
        assert(isset($metadata['publication']));
        $this->id = $metadata['id'] ?? null;
        $this->key = $metadata['key'];
        $this->publication->fill($metadata['publication']);
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
