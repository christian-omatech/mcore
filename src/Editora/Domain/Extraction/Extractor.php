<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Extraction;

use Omatech\Mcore\Editora\Domain\Attribute\Attribute;
use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection as InstanceAttributes;
use Omatech\Mcore\Editora\Domain\Extraction\Attribute as QueryAttribute;
use Omatech\Mcore\Editora\Domain\Extraction\Instance as ExtractionInstance;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use function DeepCopy\deep_copy;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Extractor
{
    private Query $query;
    private Instance $instance;

    /**
     * @var array<Query>
     */
    private array $relations;

    /**
     * @param Query $query
     * @param Instance $instance
     * @param array<Query> $relations
     */
    public function __construct(Query $query, Instance $instance, array $relations = [])
    {
        $this->query = $query;
        $this->instance = $instance;
        $this->relations = $relations;
    }

    public function extract(): ExtractionInstance
    {
        return $this->extractInstance($this->query, $this->instance, $this->relations);
    }

    /**
     * @param Query $query
     * @param Instance $instance
     * @param array<Query> $instanceRelations
     *
     * @return \Omatech\Mcore\Editora\Domain\Extraction\Instance
     */
    private function extractInstance(
        Query $query,
        Instance $instance,
        array $instanceRelations = []
    ): ExtractionInstance {
        return new ExtractionInstance([
            'key' => $instance->key(),
            'attributes' => $this->extractAttributes(
                $query->attributes(),
                $instance->attributes(),
            ),
            'relations' => $this->extractRelations($query->relations(), $instanceRelations),
        ]);
    }

    /**
     * @param array<QueryAttribute> $queryAttributes
     * @param InstanceAttributes $instanceAttributes
     *
     * @return array<QueryAttribute>
     */
    private function extractAttributes(
        array $queryAttributes,
        InstanceAttributes $instanceAttributes
    ): array {
        return reduce(function (
            array $acc,
            string $language
        ) use ($queryAttributes, $instanceAttributes) {
            $acc[$language] = $this->extractAttributesByLanguage(
                $language,
                $queryAttributes,
                $instanceAttributes
            );
            return $acc;
        }, $this->query->languages(), []);
    }

    /**
     * @param string $language
     * @param array<QueryAttribute> $queryAttributes
     * @param InstanceAttributes $instanceAttributes
     *
     * @return array<QueryAttribute>
     */
    private function extractAttributesByLanguage(
        string $language,
        array $queryAttributes,
        InstanceAttributes $instanceAttributes
    ): array {
        return reduce(function (
            array $acc,
            Attribute $instanceAttribute
        ) use ($language, $queryAttributes): array {
            $queryAttribute = $this->searchForQueryAttribute($queryAttributes, $instanceAttribute);
            if ($queryAttribute) {
                $acc[] = $this->fillValueForQueryAttribute(
                    $language,
                    $instanceAttribute,
                    $queryAttribute
                );
            }
            return $acc;
        }, $instanceAttributes->get(), []);
    }

    /**
     * @param array<QueryAttribute> $queryAttributes
     * @param Attribute $instanceAttribute
     *
     * @return QueryAttribute|null
     */
    private function searchForQueryAttribute(
        array $queryAttributes,
        Attribute $instanceAttribute
    ): ?QueryAttribute {
        $queryAttribute = search(
            static function (QueryAttribute $queryAttribute) use ($instanceAttribute): bool {
                return $instanceAttribute->key() === $queryAttribute->key();
            },
            $queryAttributes
        );
        if (! count($queryAttributes)) {
            $queryAttribute = new QueryAttribute($instanceAttribute->key(), []);
        }
        return $queryAttribute;
    }

    private function fillValueForQueryAttribute(
        string $language,
        Attribute $instanceAttribute,
        QueryAttribute $queryAttribute
    ): QueryAttribute {
        $queryAttribute->setAttributes(
            $this->extractAttributesByLanguage(
                $language,
                $queryAttribute->attributes(),
                $instanceAttribute->attributes()
            )
        );
        $queryAttribute->setValue($this->extractValue($instanceAttribute, $language));
        return deep_copy($queryAttribute);
    }

    /**
     * @param Attribute $attribute
     * @param string $language
     *
     * @return array{uuid:string|null, value:mixed|null}
     */
    private function extractValue(Attribute $attribute, string $language): array
    {
        $values = [
            $language => $attribute->values()->language($language)?->value(),
            '*' => $attribute->values()->language('*')?->value(),
            '+' => $attribute->values()->language('+')?->value(),
        ];
        return [
            'uuid' => $attribute->values()->language($language)?->uuid(),
            'value' => first(filter(static fn ($value) => ! is_null($value), $values)),
        ];
    }

    private function extractRelations(array $queryRelations, array $relations): array
    {
        return reduce(function (
            array $acc,
            RelationsResults $relation
        ) use ($queryRelations): array {
            $queryRelation = search(static function ($query) use ($relation): bool {
                return $query->param('key') === $relation->key() &&
                    $query->param('type') === $relation->type();
            }, $queryRelations);
            if ($queryRelation) {
                $acc[] = (new Relation($relation->key(), $relation->type()))
                    ->setInstances($this->addInstancesRelation($queryRelation, $relation));
            }
            return $acc;
        }, $relations, []);
    }

    private function addInstancesRelation(Query $queryRelation, RelationsResults $relation): array
    {
        return reduce(
            function (array $acc, Instance $instance) use ($queryRelation, $relation): array {
                $acc[] = $this->extractInstance($queryRelation, $instance, $relation->relations());
                return $acc;
            },
            $relation->instances(),
            []
        );
    }
}
