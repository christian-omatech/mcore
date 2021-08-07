<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance;

use Omatech\Ecore\Editora\Domain\Attribute\AttributeBuilder;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
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
    private InstanceCacheInterface $instanceCache;

    public function __construct(?InstanceCacheInterface $instanceCache = null)
    {
        $this->instanceCache = $instanceCache ?? InstanceCache::getInstance();
    }

    public function build(): Instance
    {
        $this->ensureBuilderIsValid();
        return $this->instanceCache->get($this->className) ?? $this->buildInstance();
    }

    private function ensureBuilderIsValid(): void
    {
        if (! count($this->languages)) {
            throw new InvalidLanguagesException();
        }
        if ($this->className === '') {
            throw new InvalidClassNameException();
        }
        if (! count($this->structure)) {
            throw new InvalidStructureException();
        }
    }

    private function buildInstance(): Instance
    {
        $instance = [
            'metadata' => [
                'key' => $this->className,
                'relations' => $this->normalizeRelations(),
            ],
            'attributes' => (new AttributeBuilder())
                ->setLanguages($this->languages)
                ->setAttributes($this->structure['attributes'])
                ->build(),
        ];
        $instance = new class($instance) extends Instance {
        };
        $this->instanceCache->put($this->className, $instance);
        return $instance;
    }

    private function normalizeRelations(): array
    {
        return array_values(map(static function (array $relations, string $key): array {
            return [
                'key' => Stringify::getInstance()->slug($key),
                'classes' => map(static function ($class): string {
                    return Stringify::getInstance()->slug($class);
                }, $relations),
            ];
        }, $this->structure['relations'] ?? []));
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
