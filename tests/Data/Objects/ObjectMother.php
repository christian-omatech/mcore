<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Contracts\ExtractionCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\reduce;

abstract class ObjectMother
{    
    protected array $structure;
    protected array $languages;
    protected array $instances = [];
    protected Generator $faker;

    public function __construct(array $languages = ['es', 'en']) {
        $this->languages = $languages;
        $this->structure = (require __DIR__.'/../data.php')['classes'];
        $this->faker = Factory::create();
    }

    protected function build(string $className): Instance
    {        
        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        return (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$className])
            ->setClassName($className)
            ->build();
    }

    abstract public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []);

    public function asKey(string $key): array
    {
        return $this->get(1, $key);
    }

    public function create(int $instancesNumber = 1, ?string $key = null, ?array $relations = [])
    {
        $createdInstances = [];
        $createdRelatedInstances = [];
        for ($i = 1; $i <= $instancesNumber; $i++) {
            $relatedInstances = reduce(function (array $acc, array $instancesToCreate, string $relationKey) {
                $acc[$relationKey] = reduce(function (array $acc, string $class) use ($instancesToCreate) {
                    $object = (new $class());
                    return array_merge_recursive($acc, $object->create(
                        $instancesToCreate['instances'],
                        null,
                        $instancesToCreate['relations'] ?? []
                    ));
                }, $this->availableRelations[$relationKey] ?? [], []);
                return $acc;
            }, $relations, []);
            $relatedInstances = array_filter($relatedInstances);

            $relationsIds = reduce(static function (array $acc, array $instances, string $relationKey) {
                $current[$relationKey] = reduce(static function (array $acc, $instance) {
                    return $acc + [$instance->id() => $instance->data()['classKey']];
                }, $instances['instances'], []);
                return array_merge($acc, $current);
            }, $relatedInstances, []);

            $createdInstances = array_merge($createdInstances, $this->get(1, $key, $relationsIds));
            $createdRelatedInstances = array_merge_recursive($createdRelatedInstances, $relatedInstances);
        }

        return [
            'instances' => $createdInstances,
            'relations' => $createdRelatedInstances,
        ];
    }

    public static function extraction(array $instances, array $fields, string $language, array $relations = []): array
    {
        $extraction = reduce(static function (array $acc, Instance $instance) use ($fields, $language, $relations): array {
            $acc[] = [
                'key' => $instance->key(),
                'attributes' => self::extractAttributes($instance->attributes(), $fields, $language),
                'relations' => $relations,
            ];
            return $acc;
        }, $instances, []);
        return count($extraction) < 2 ? first($extraction) ?? [] : $extraction;
    }

    private static function extractAttributes(AttributeCollection $attributes, array $fields, string $language)
    {
        return reduce(static function (array $acc, string|array $field, string|int $key) use ($attributes, $fields, $language) {
            $sub = [];
            $currentField = is_array($field) ? $key : $field;
            if (is_array($field)) {
                $sub = self::extractAttributes($attributes->find($currentField)?->attributes(), $fields[$currentField], $language);
            }
            $value = filter(static fn ($value) => ! is_null($value), [
                $language => $attributes->find($currentField)?->value($language)?->value(),
                '+' => $attributes->find($currentField)?->value('+')?->value(),
                '*' => $attributes->find($currentField)?->value('*')?->value(),
            ]);
            $key = array_key_first($value) ?? $language;
            $acc[] = [
                'id' => $attributes->find($currentField)?->value($key)?->id(),
                'key' => $currentField,
                'value' => $value[$key] ?? null,
                'attributes' => $sub,
            ];
            return $acc;
        }, $fields, []);
    }
}
