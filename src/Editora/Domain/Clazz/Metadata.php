<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Clazz;

final class Metadata
{
    private string $name;
    private string $caption;

    public function __construct(string $name, string $caption)
    {
        $this->name = $name;
        $this->caption = $caption;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'caption' => $this->caption,
        ];
    }
}
