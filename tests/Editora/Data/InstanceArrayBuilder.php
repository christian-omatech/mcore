<?php

namespace Tests\Editora\Data;

use function Lambdish\Phunctional\map;

class InstanceArrayBuilder
{
    private array $languages = [
        ['language' => 'es'],
        ['language' => 'en']
    ];
    private array $instance = [
        'relations' => []
    ];

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

    public function addAttribute(
        string $key,
        string $type,
        array  $values,
        array  $fn = []
    ): self
    {
        $this->instance['attributes'][] = $this->attribute($key, $type, $values, $fn);
        return $this;
    }

    public function addSubAttribute(string $key, string $type, array $values, array $fn = []): array
    {
        return $this->attribute($key, $type, $values, $fn);
    }

    public function addRelation(): self
    {
        $this->instance['relations'] = [];
        return $this;
    }

    private function attribute(string $key, string $type, array $values, array $fn = []): array
    {
        return [
            'key' => $key,
            'type' => $type,
            'values' => map(static function (array $value) {
                return array_merge([
                    'uuid' => null,
                    'language' => '',
                    'rules' => [],
                    'configuration' => [],
                    'value' => null,
                    'extraData' => [],
                ], $value);
            }, ($values === []) ? $this->languages : $values),
            'attributes' => array_reduce($fn, function (array $acc, callable $fn) {
                $acc[] = $fn($this);
                return $acc;
            }, [])
        ];
    }

    public function build(): array
    {
        return $this->instance;
    }
}