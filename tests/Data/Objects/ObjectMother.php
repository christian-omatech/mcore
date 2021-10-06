<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use Faker\Factory;
use Faker\Generator;
use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\flatten;
use function Lambdish\Phunctional\reduce;

abstract class ObjectMother
{
    protected array $structure;
    protected array $languages;
    protected array $instances = [];
    protected Generator $faker;

    public function __construct(array $languages = ['es', 'en'])
    {
        $this->languages = $languages;
        $this->structure = (require __DIR__.'/../data.php')['classes'];
        $this->faker = Factory::create();
    }

    protected function build(string $className): Instance
    {
        return (new InstanceBuilder())
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
        $relationsInstances = reduce(function (array $acc, array $relation, string $relationKey) {
            $acc[$relationKey] = reduce(function(array $acc, $relationClass) use ($relation) {
                $relatedObject = new $relationClass;
                $acc[] = array_merge($relatedObject->create(
                    $relation['instances'],
                    null,
                    $relation['relations'] ?? [],
                ), ['object' => $relatedObject]);
                return $acc;
            }, $this->availableRelations[$relationKey], []);
            return $acc;
        }, $relations, []);

        $relations = reduce(static function (array $acc, array $instances, string $relationKey) {
            $current = reduce(function (array $acc, $instances) use ($relationKey) {
                $current[$relationKey] = reduce(static fn ($acc, Instance $instance) => $acc + [
                    $instance->id() => $instance->data()['classKey'],
                ], $instances['instances'], []);
                return array_merge_recursive($current, $acc);
            }, $instances, []);
            return array_merge($acc, $current);
        }, $relationsInstances, []);

        return [
            'instances' => $this->get($instancesNumber, $key, $relations),
            'relations' => $relationsInstances,
        ];
    }

    public function extraction(array $fields, string $language, array $relations = []): array
    {
        $extraction = reduce(function (array $acc, Instance $instance) use ($fields, $language, $relations): array {
            $acc[] = [
                'key' => $instance->key(),
                'attributes' => $this->extractAttributes($instance->attributes(), $fields, $language),
                'relations' => $relations,
            ];
            return $acc;
        }, $this->instances, []);
        return count($extraction) < 2 ? first($extraction) ?? [] : $extraction;
    }

    private function extractAttributes(AttributeCollection $attributes, array $fields, string $language)
    {
        return reduce(function (array $acc, string|array $field, string|int $key) use ($attributes, $fields, $language) {
            $sub = [];
            $currentField = is_array($field) ? $key : $field;
            if (is_array($field)) {
                $sub = $this->extractAttributes($attributes->find($currentField)?->attributes(), $fields[$currentField], $language);
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
