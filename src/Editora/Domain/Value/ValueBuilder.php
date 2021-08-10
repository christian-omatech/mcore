<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidValueTypeException;
use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

final class ValueBuilder
{
    private const NAMESPACE = 'Omatech\\Ecore\\Editora\\Domain\\Value\\Types\\';
    private array $languages;
    private string $key;

    public function build(): array
    {
        $this->normalizeLanguages();
        $this->normalizeValues();
        return $this->instanceValues();
    }

    private function normalizeLanguages(): void
    {
        $this->values['languages'] = $this->values['languages'] ?? [];
        if (! array_key_exists('*', $this->values['languages'])) {
            if (array_key_exists('+', $this->values['languages'])) {
                $this->languages['+'] = [];
            }
            $this->values['languages'] = map(function ($values, $language): array {
                return $this->values['languages'][$language] ?? $values;
            }, $this->languages);
        }
    }

    private function normalizeValues(): void
    {
        $this->values = map(function ($properties): array {
            return $this->defaultsToValue($properties ?? []);
        }, $this->values['languages']);
    }

    private function defaultsToValue(array $properties): array
    {
        return map(function ($value, $key) use ($properties): string | array {
            return $properties[$key] ?? $this->values[$key] ?? $value;
        }, [
            'configuration' => [],
            'rules' => [],
            'type' => 'Value',
        ]);
    }

    private function instanceValues(): array
    {
        return flat_map(function ($properties, $language): BaseValue {
            if (! class_exists($properties['type'])) {
                $properties['type'] = self::NAMESPACE . $properties['type'];
                if (! class_exists($properties['type'])) {
                    InvalidValueTypeException::withType($properties['type']);
                }
            }
            return new $properties['type']($this->key, $language, $properties);
        }, $this->values);
    }

    public function setLanguages(array $languages): ValueBuilder
    {
        $this->languages = $languages;
        return $this;
    }

    public function setValues(array $values): ValueBuilder
    {
        $this->values = $values;
        return $this;
    }

    public function setKey(string $key): ValueBuilder
    {
        $this->key = $key;
        return $this;
    }
}
