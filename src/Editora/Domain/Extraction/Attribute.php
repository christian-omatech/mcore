<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\map;

final class Attribute extends QueryAttribute
{
    private ?string $uuid = null;
    private mixed $value = null;

    public function __construct(string $key, Value $value, array $attributes)
    {
        parent::__construct($key, $attributes);
        $this->uuid = $value->uuid();
        $this->value = $value->value();
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'key' => $this->key,
            'value' => $this->value,
            'attributes' => map(
                static fn (Attribute $attribute): array => $attribute->toArray(),
                $this->attributes
            ),
        ];
    }
}
