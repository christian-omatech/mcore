<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use Omatech\Mcore\Editora\Domain\Attribute\Attribute;
use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Attribute as QueryAttribute;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Extractor
{
    private Query $query;
    private Instance $instance;
    /** @var array<Relation> $relations */
    private array $relations;

    public function __construct(Query $query, Instance $instance, array $relations = [])
    {
        $this->query = $query;
        $this->instance = $instance;
        $this->relations = $relations;
    }

    public function extract(): Query
    {
        return $this->parse($this->query, $this->instance, $this->relations);
    }

    private function parse(Query $query, Instance $instance, array $instanceRelations = []): Query
    {
        $query->setAttributes($this->getAttributes($query->attributes(), $instance->attributes()));
        $query->setRelations($this->getRelations($query->relations(), $instanceRelations));
        return $query;
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
            $acc[$value->language()] = $value->value();
            return $acc;
        }, $attribute->values()->get(), []);
        $value = $values[$this->query->language()] ?? null;
        $value = $values['*'] ?? $value;
        $value = $value ?? $values['+'] ?? null;
        $queryAttribute->setValue($value);
        return $queryAttribute;
    }

    private function getRelations(array $queryRelations, array $instanceRelations): array
    {
        return map(function (array $relation) use ($queryRelations): array {
            return $this->fillQueryRelation($queryRelations, $relation);
        }, $instanceRelations);
    }

    private function fillQueryRelation(array $queryRelations, array $relation): array
    {
        return reduce(function (array $acc, Relation $queryRelation) use ($relation): array {
            $queryInstance = reduce(function ($acc, Query $query) use ($relation): array {
                $instance = search(static function ($instance) use ($query): bool {
                    return $instance->key() === $query->key();
                }, $relation['instances']);
                if ($instance) {
                    $acc[] = $this->parse($query, $instance, $relation['relations']);
                }
                return $acc;
            }, $queryRelation->instances(), []);
            if (count($queryInstance)) {
                $acc = $queryInstance;
            }
            return $acc;
        }, $queryRelations, []);
    }
}
