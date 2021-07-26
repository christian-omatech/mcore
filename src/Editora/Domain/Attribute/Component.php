<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Attribute;

class Component
{
    private string $type;
    private string $caption;

    public function __construct(
        string $type,
        string $caption
    ) {
        $this->type = $type;
        $this->caption = $caption;
    }
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'caption' => $this->caption,
        ];
    }
}
