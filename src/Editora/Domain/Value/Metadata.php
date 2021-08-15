<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value;

final class Metadata
{
    private string $attributeKey;
    private string $language;
    private ?int $id = null;

    public function __construct(string $attributeKey, string $language)
    {
        $this->attributeKey = $attributeKey;
        $this->language = $language;
    }

    public function fill(?int $id): void
    {
        $this->id = $id;
    }

    public function attributeKey(): string
    {
        return $this->attributeKey;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function id(): ?int
    {
        return $this->id;
    }
}
