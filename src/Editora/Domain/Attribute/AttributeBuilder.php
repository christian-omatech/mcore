<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Attribute;

use Omatech\Ecore\Editora\Domain\Value\ValueBuilder;
use Omatech\Ecore\Shared\Utils\Stringify;
use function Lambdish\Phunctional\map;

final class AttributeBuilder
{
    private array $languages;
    private array $attributes;

    public function build(): array
    {
        return array_values(map(function ($properties, $key) {
            $properties = $this->defaultsToAttribute($key, $properties);
            $properties['values'] = (new ValueBuilder())
                ->setLanguages($this->languages)
                ->setValues($properties['values'])
                ->build();
            $properties['attributes'] = (new AttributeBuilder())
                ->setLanguages($this->languages)
                ->setAttributes($properties['attributes'])
                ->build();
            return new Attribute($properties);
        }, $this->attributes));
    }

    private function defaultsToAttribute(string $key, ?array $properties): array
    {
        $key = Stringify::getInstance()->slug($key);
        $type = $properties['type'] ?? 'string';
        return [
            'key' => $key,
            'type' => $type,
            'caption' => $properties['caption'] ?? "attribute.{$key}.{$type}",
            'values' => $properties['values'] ?? [],
            'attributes' => $properties['attributes'] ?? [],
        ];
    }

    public function setAttributes(array $attributes): AttributeBuilder
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setLanguages(array $languages): AttributeBuilder
    {
        $this->languages = $languages;
        return $this;
    }
}
