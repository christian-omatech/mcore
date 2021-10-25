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
            if ($queryAttribute) {
                $acc[] = $this->fillValueForQueryAttribute($attribute, $queryAttribute);
            }
            return $acc;
        }, $attributes->get(), []);
    }

    private function searchForQueryAttribute(
        Attribute $attribute,
        array $queryAttributes
    ): ?QueryAttribute {
        $queryAttribute = search(
            static function (QueryAttribute $queryAttribute) use ($attribute): bool {
                return $attribute->key() === $queryAttribute->key();
            },
            $queryAttributes
        );
        if (! count($queryAttributes)) {
            $queryAttribute = new QueryAttribute($attribute->key(), []);
        }
        return $queryAttribute;
    }

    private function fillValueForQueryAttribute(
        Attribute $attribute,
        QueryAttribute $queryAttribute
    ): QueryAttribute {
        $values = reduce(static function (array $acc, BaseValue $value): array {
            $acc[$value->language()] = ['id' => $value->id(), 'value' => $value->value()];
            return $acc;
        }, $attribute->values()->get(), []);
        $queryAttribute->setAttributes(
            $this->getAttributes($queryAttribute->attributes(), $attribute->attributes())
        );
        $queryAttribute->setValue($this->getValue($values));
        return deep_copy($queryAttribute);
    }

    private function getValue(array $values): array
    {
        $possibleValues = filter(static fn ($value) => ! is_null($value), [
            $values[$this->query->param('language')] ?? null,
            $values['*'] ?? null,
            $values['+'] ?? null,
        ]);
        return first(filter(static function ($value): bool {
            return isset($value['value']);
        }, $possibleValues)) ?? first($possibleValues);
    }

    private function matchRelations(array $queryRelations, array $relations): array
    {
        return reduce(function (
            array $acc,
            array $relation,
            string $key
        ) use ($queryRelations): array {
            $acc[$key] = $this->matchRelation($queryRelations, $relation, $key);
            return $acc;
        }, $relations, []);
    }

    private function matchRelation(array $queryRelations, array $relation, string $key): array
    {
        return reduce(function (
            array $acc,
            array $relationInstances,
            string $type
        ) use ($key, $queryRelations): array {
            $queryRelation = search(static function ($query) use ($key, $type): bool {
                return $query->param('class') === $key && $query->param('type') === $type;
            }, $queryRelations);
            if ($queryRelation) {
                $acc[$type] = $this->addInstancesRelation($queryRelation, $relationInstances);
            }
            return $acc;
        }, $relation, []);
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
}
