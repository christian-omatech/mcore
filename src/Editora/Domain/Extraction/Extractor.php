<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use Omatech\MageCore\Editora\Domain\Attribute\Attribute;
use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection as InstanceAttributes;
use Omatech\MageCore\Editora\Domain\Extraction\Attribute as QueryAttribute;
use Omatech\MageCore\Editora\Domain\Extraction\Instance as ExtractionInstance;
use Omatech\MageCore\Editora\Domain\Instance\Instance;
use function DeepCopy\deep_copy;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Extractor
{
    private readonly Query $query;
    private readonly Instance $instance;
    private readonly array $relations;

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

    private function searchForQueryAttribute(
        array $queryAttributes,
        Attribute $instanceAttribute
    ): ?QueryAttribute {
        $queryAttribute = search(
            static fn (QueryAttribute $queryAttribute): bool => $instanceAttribute->key() === $queryAttribute->key(),
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
            $queryRelation = search(static fn ($query): bool => $query->param('key') === $relation->key() &&
                $query->param('type') === $relation->type(), $queryRelations);
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
