<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Attribute;

final class Component
{
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
