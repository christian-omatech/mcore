<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance;

use Omatech\Ecore\Editora\Domain\Attribute\AttributeBuilder;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\Ecore\Shared\Utils\Stringify;
use function Lambdish\Phunctional\map;

final class InstanceBuilder
{
    private array $languages = [];
    private array $structure = [];
    private string $className = '';

    public function build(): Instance
    {
        $this->ensureBuilderIsValid();

        $instance = [
            'metadata' => [
                'className' => $this->className,
                'caption' => $this->structure['caption'] ?? "class.{$this->className}",
                'relations' => $this->normalizeRelations(),
            ],
            'attributes' => (new AttributeBuilder())
                ->setLanguages($this->languages)
                ->setAttributes($this->structure['attributes'])
                ->build(),
        ];

        return new class($instance) extends Instance {
        };
    }

    private function ensureBuilderIsValid(): void
    {
        if (!count($this->languages)) {
            throw new InvalidLanguagesException();
        }
        if (!count($this->structure)) {
            throw new InvalidStructureException();
        }
        if ($this->className === '') {
            throw new InvalidClassNameException();
        }
    }

    private function normalizeRelations(): array
    {
        return array_values(map(static function ($relation, $key) {
            return [
                'key' => Stringify::getInstance()->slug($key),
                'classes' => map(static function ($class) {
                    return Stringify::getInstance()->slug($class);
                }, $relation ?? [])
            ];
        }, $this->structure['relations'] ?? [])
    );
    }

    public function setLanguages(array $languages): InstanceBuilder
    {
        $this->languages = array_fill_keys($languages, []);
        return $this;
    }

    public function setStructure(array $structure): InstanceBuilder
    {
        $this->structure = $structure;
        return $this;
    }

    public function setClassName(string $className): InstanceBuilder
    {
        $this->className = Stringify::getInstance()->slug($className);
        return $this;
    }
}
