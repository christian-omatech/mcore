<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use Omatech\Mcore\Editora\Domain\Attribute\Attribute;
use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Attribute as QueryAttribute;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Instance as ExtractionInstance;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use function DeepCopy\deep_copy;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Extractor
{
    private Query $query;
    private Instance $instance;
    /** @var array<Query> $relations */
    private array $relations;

    public function __construct(Query $query, Instance $instance, array $relations = [])
    {
        $this->query = $query;
        $this->instance = $instance;
        $this->relations = $relations;
    }

    public function extract(): ExtractionInstance
    {
        return $this->parse($this->query, $this->instance, $this->relations);
    }

    private function parse(
        Query $query,
        Instance $instance,
        array $instanceRelations = []
    ): ExtractionInstance {
        return new ExtractionInstance([
            'key' => $instance->key(),
            'attributes' => $this->getAttributes($query->attributes(), $instance->attributes()),
            'relations' => $this->matchRelations($query->relations(), $instanceRelations),
        ]);
    }

    private function getAttributes(array $queryAttributes, AttributeCollection $attributes): array
    {
        return reduce(function (array $acc, Attribute $attribute) use ($queryAttributes): array {
            $queryAttribute = $this->searchForQueryAttribute($attribute, $queryAttributes);
            if (count($queryAttributes) === 0) {
                $queryAttribute = new QueryAttribute($attribute->key(), []);
            }
            if ($queryAttribute) {
                $queryAttribute->setAttributes($this->getAttributes(
                    $queryAttribute->attributes(),
                    $attribute->attributes()
                ));
                $acc[] = $this->fillValueForQueryAttribute($attribute, $queryAttribute);
            }
            return $acc;
        }, $attributes->get(), []);
    }

    private function searchForQueryAttribute(
        Attribute $attribute,
        array $queryAttributes
    ): ?QueryAttribute {
        return search(static function (QueryAttribute $queryAttribute) use ($attribute): bool {
            return $attribute->key() === $queryAttribute->key();
        }, $queryAttributes);
    }

    private function fillValueForQueryAttribute(
        Attribute $attribute,
        QueryAttribute $queryAttribute
    ): QueryAttribute {
        $values = reduce(static function (array $acc, BaseValue $value): array {
            $acc[$value->language()]['id'] = $value->id();
            $acc[$value->language()]['value'] = $value->value();
            return $acc;
        }, $attribute->values()->get(), []);
        $value = first(filter(static function ($value) {
            return isset($value['value']);
        }, [
            $values[$this->query->param('language')] ?? null,
            $values['*'] ?? null,
            $values['+'] ?? null,
        ]));
        $queryAttribute->setValue($value['id'] ?? null, $value['value'] ?? null);
        return deep_copy($queryAttribute);
    }

    private function matchRelations(array $queryRelations, array $instancesRelations): array
    {
        return reduce(function (
            array $acc,
            array $instancesRelation,
            string $key
        ) use ($queryRelations): array {
            $queryRelation = search(static function ($query) use ($key) {
                return $query->param('class') === $key;
            }, $queryRelations);
            if ($queryRelation) {
                $acc[$key] = $this->addInstancesRelation($queryRelation, $instancesRelation);
            }
            return $acc;
        }, $instancesRelations, []);
    }

    private function addInstancesRelation(Query $queryRelation, array $instancesRelation): array
    {
        return reduce(function (
            array $acc,
            Instance $instance
        ) use ($queryRelation, $instancesRelation): array {
            $acc[] = $this->parse($queryRelation, $instance, $instancesRelation['relations']);
            return $acc;
        }, $instancesRelation['instances']->instances(), []);
    }

    public function query(): Query
    {
        return $this->query;
    }
}
