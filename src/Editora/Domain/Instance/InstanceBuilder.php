<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Attribute\AttributeBuilder;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\Mcore\Shared\Utils\Utils;
use function Lambdish\Phunctional\map;

final class InstanceBuilder
{
    private array $languages = [];
    private array $structure = [];
    private string $className = '';
    private InstanceCacheInterface $instanceCache;

    public function __construct(InstanceCacheInterface $instanceCache)
    {
        $this->instanceCache = $instanceCache;
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

        $instance = new Instance($instance);
        $this->instanceCache->put($this->className, $instance);
        return $instance;
    }

    private function normalizeRelations(): array
    {
        return map(static function (array $relations, string &$key): array {
            $key = Utils::getInstance()->slug($key);
            return map(static function ($class): string {
                return Utils::getInstance()->slug($class);
            }, $relations);
        }, $this->structure['relations'] ?? []);
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
        $this->className = Utils::getInstance()->slug($className);
        return $this;
    }
}
