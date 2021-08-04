<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidValueTypeException;
use function Lambdish\Phunctional\map;

final class ValueBuilder
{
    private const NAMESPACE = 'Omatech\\Ecore\\Editora\\Domain\\Value\\Types\\';
    private array $languages;
    private array $values;

    public function build(): array
    {
        $this->normalizeLanguages();
        $this->normalizeValues();
        return $this->instanceValues();
    }

    private function normalizeLanguages(): void
    {
        if (! isset($this->values['languages']['*'])) {
            $this->values['languages'] = map(function ($values, $language) {
                return $this->values['languages'][$language] ?? $values;
            }, $this->languages);
        }
    }

    private function normalizeValues(): void
    {
        $this->values = map(function ($properties) {
            return $this->defaultsToValue($properties ?? []);
        }, $this->values['languages']);
    }

    private function defaultsToValue(array $properties): array
    {
        return map(function ($value, $key) use ($properties) {
            return $properties[$key] ?? $this->values[$key] ?? $value;
        }, [
            'configuration' => [],
            'rules' => [],
            'type' => 'Value',
        ]);
    }

    private function instanceValues(): array
    {
        return array_values(map(function ($properties, $language) {
            $type = $this->ensureValueTypeExists($properties['type']);
            return new $type($language, $properties);
        }, $this->values));
    }

    private function ensureValueTypeExists(string $type): string
    {
        if (! class_exists($type)) {
            $type = self::NAMESPACE . $type;
            if (! class_exists($type)) {
                InvalidValueTypeException::withType($type);
            }
        }
        return $type;
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
}
