<?php

namespace Tests\Editora\Data;

use function Lambdish\Phunctional\map;

class InstanceArrayBuilder
{
    private array $instance;

    public function addClassKey(string $classKey): self
    {
        $this->instance['class']['key'] = $classKey;
        return $this;
    }

    public function addClassRelations(string $relationKey, array $allowedClasses): self
    {
        $this->instance['class']['relations'][] = [
            'key' => $relationKey,
            'classes' => $allowedClasses
        ];
        return $this;
    }

    public function addPublication(string $status): self
    {
        $this->instance['metadata'] = [
            'uuid' => null,
            'key' => '',
            'publication' => [
                'status' => $status,
                'startPublishingDate' => null,
                'endPublishingDate' => null
            ]
        ];
        return $this;
    }

    public function addAttribute(string $key, string $type, array $values): self
    {
        $this->instance['attributes'][] = [
            'key' => $key,
            'type' => $type,
            'values' => map(static function(array $value) {
                return array_merge([
                    'uuid' => null,
                    'language' => '',
                    'rules' => [],
                    'configuration' => [],
                    'value' => null,
                    'extraData' => [],
                ], $value);
            }, $values),
            'attributes' => []
        ];
        return $this;
    }

    public function build(): array
    {
        return $this->instance;
    }
}