<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Query;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Results;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use function Lambdish\Phunctional\map;

class InstanceExtractionTest extends TestCase
{
    /** @test */
    public function extractMultipleInstancesWithSingleQuery(): void
    {
        $query = '{
            class(key: InstanceKey, preview: false, language: es) {
                AttributeOne
            }
            class(key: InstanceKey, preview: true, language: en) {
                AttributeTwo
            }
        }';

        $queries = (new QueryParser())->parse($query);
        $instance1 = $this->instance(
            'instance-key1',
            [
                'attributes' => [
                    'AttributeOne' => [],
                ],
            ],
            [
                'attribute-one' => [
                    'values' => [
                        [
                            'id' => 1,
                            'language' => 'es',
                            'value' => 'value-one',
                            'attributes' => [],
                        ],
                    ],
                ],
            ],
            []
        );
        $instance2 = $this->instance(
            'instance-key2',
            [
                'attributes' => [
                    'AttributeTwo' => [],
                ],
            ],
            [
                'attribute-two' => [
                    'values' => [
                        [
                            'id' => 2,
                            'language' => 'en',
                            'value' => 'value-two',
                            'attributes' => [],
                        ],
                    ],
                ],
            ],
            []
        );
        $instances = [$instance1, $instance2];
        $instances = map(static function (Query $query, int $index) use ($instances) {
            return (new Extractor($query, $instances[$index], []))->extract()->toArray();
        }, $queries, []);

        $this->assertEquals([
            [
                'key' => 'instance-key1',
                'attributes' => [
                    [
                        'id' => 1,
                        'key' => 'attribute-one',
                        'value' => 'value-one',
                        'attributes' => [],
                    ],
                ],
                'relations' => [],
            ],
            [
                'key' => 'instance-key2',
                'attributes' => [
                    [
                        'id' => 2,
                        'key' => 'attribute-two',
                        'value' => 'value-two',
                        'attributes' => [],
                    ],
                ],
                'relations' => [],
            ],
        ], $instances);
    }

    /** @test */
    public function extractInstanceWithQueryExtraction(): void
    {
        $query = '{
            class(key: InstanceKey, preview: false, language: es) {
                DefaultAttribute
                AnotherAttribute
                AnotherOtherAttribute
                MultiAttribute
                AnotherMultiAttribute
                RelationKey1(limit: 1) {
                    OneAttribute {
                        SubOneAttribute
                    }
                }
                RelationKey2(limit: 1) {
                    RelationKey3(limit: 1)
                }
            }
        }';
        $query = (new QueryParser())->parse($query)[0];

        $instance = $this->instance('instance-key', [
            'attributes' => [
                'DefaultAttribute' => [
                    'attributes' => [
                        'SubDefaultAttribute' => [],
                    ],
                ],
                'AnotherAttribute' => [
                    'values' => [
                        'languages' => [
                            '+' => [],
                        ],
                    ],
                ],
                'AnotherOtherAttribute' => [
                    'values' => [
                        'languages' => [
                            '+' => [],
                        ],
                    ],
                ],
                'MultiAttribute' => [
                    'values' => [
                        'languages' => [
                            '*' => [],
                        ],
                    ],
                ],
                'AnotherMultiAttribute' => [
                    'values' => [
                        'languages' => [
                            '*' => [],
                        ],
                    ],
                ],
                'NonQueryAttribute' => [],
            ],
        ], [
            'default-attribute' => [
                'values' => [
                    [
                        'id' => 1,
                        'language' => 'es',
                        'value' => 'hola',
                    ], [
                        'language' => 'en',
                        'value' => 'hello',
                    ],
                ],
            ],
            'another-attribute' => [
                'values' => [
                    [
                        'language' => 'es',
                        'value' => null,
                    ], [
                        'language' => 'en',
                        'value' => null,
                    ], [
                        'language' => '+',
                        'value' => 'por defecto',
                    ],
                ],
            ],
            'another-other-attribute' => [
                'values' => [
                    [
                        'language' => 'es',
                        'value' => 'sin defecto',
                    ], [
                        'language' => 'en',
                        'value' => null,
                    ], [
                        'language' => '+',
                        'value' => 'por defecto',
                    ],
                ],
            ],
            'multi-attribute' => [
                'values' => [
                    [
                        'language' => '*',
                        'value' => 'multi-value',
                    ],
                ],
            ],
            'another-multi-attribute' => [
                'values' => [
                    [
                        'language' => '*',
                        'value' => null,
                    ],
                ],
            ],
        ], []);
        $relations = [
            'relation-key1' => [
                'instances' => new Results([
                    $this->instance('instance-key2', [
                        'attributes' => [
                            'OneAttribute' => [
                                'attributes' => [
                                    'SubOneAttribute' => [],
                                ],
                            ],
                        ],
                    ], [
                        'one-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola',
                                ], [
                                    'language' => 'en',
                                    'value' => 'hello',
                                ],
                            ],
                            'attributes' => [],
                        ],
                    ], []),
                    $this->instance('instance-key2', [
                        'attributes' => [
                            'OneAttribute' => [
                                'attributes' => [
                                    'SubOneAttribute' => [],
                                ],
                            ],
                        ],
                    ], [
                        'one-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola',
                                ], [
                                    'language' => 'en',
                                    'value' => 'hello',
                                ],
                            ],
                            'attributes' => [],
                        ],
                    ], []),
                ], null),
                'relations' => [],
            ],
            'relation-key2' => [
                'instances' => new Results([
                    $this->instance('instance-key3', [
                        'relations' => [
                            'RelationKey3' => [
                                'ClassOne',
                            ],
                        ],
                        'attributes' => [
                            'OneAttribute' => [],
                        ],
                    ], [
                        'one-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola',
                                ], [
                                    'language' => 'en',
                                    'value' => 'hello',
                                ],
                            ],
                            'attributes' => [],
                        ],
                    ], [
                        'relation-key3' => [
                            1 => 'class-one',
                        ],
                    ]),
                ], null),
                'relations' => [
                    'relation-key3' => [
                        'instances' => new Results([
                            $this->instance('instance-key4', [
                                'attributes' => [
                                    'OneAttribute' => [],
                                ],
                            ], [
                                'one-attribute' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'adios',
                                        ], [
                                            'language' => 'en',
                                            'value' => 'bye',
                                        ],
                                    ],
                                    'attributes' => [],
                                ],
                            ], []),
                        ], null),
                        'relations' => [],
                    ],
                ],
            ],
        ];

        $this->assertNull($query->param('non-existent-param'));
        $this->assertEquals([
            'language' => 'es',
            'attributes' => [
                [
                    'key' => 'default-attribute',
                    'attributes' => [],
                ], [
                    'key' => 'another-attribute',
                    'attributes' => [],
                ], [
                    'key' => 'another-other-attribute',
                    'attributes' => [],
                ], [
                    'key' => 'multi-attribute',
                    'attributes' => [],
                ], [
                    'key' => 'another-multi-attribute',
                    'attributes' => [],
                ],
            ],
            'params' => [
                'key' => 'instance-key',
                'class' => null,
                'preview' => false,
                'language' => 'es',
                'limit' => 0,
                'page' => 1,
            ],
            'relations' => [
                [
                    'language' => 'es',
                    'attributes' => [
                        [
                            'key' => 'one-attribute',
                            'attributes' => [
                                [
                                    'key' => 'sub-one-attribute',
                                    'attributes' => [],
                                ],
                            ],
                        ],
                    ],
                    'params' => [
                        'class' => 'relation-key1',
                        'key' => null,
                        'limit' => 1,
                        'page' => 1,
                        'language' => 'es',
                        'preview' => false,
                    ],
                    'relations' => [],
                    'pagination' => null,
                ], [
                    'language' => 'es',
                    'attributes' => [],
                    'params' => [
                        'key' => null,
                        'class' => 'relation-key2',
                        'limit' => 1,
                        'page' => 1,
                        'language' => 'es',
                        'preview' => false,
                    ],
                    'relations' => [
                        [
                            'language' => 'es',
                            'attributes' => [],
                            'params' => [
                                'key' => null,
                                'class' => 'relation-key3',
                                'limit' => 1,
                                'page' => 1,
                                'language' => 'es',
                                'preview' => false,
                            ],
                            'relations' => [],
                            'pagination' => null,
                        ],
                    ],
                    'pagination' => null,
                ],
            ],
            'pagination' => null,
        ], $query->toArray());

        $extractor = new Extractor($query, $instance, $relations);
        $extraction = $extractor->extract();
        $this->assertEquals($query->toArray(), $extractor->query()->toArray());

        $this->assertEquals([
            'key' => 'instance-key',
            'attributes' => [
                [
                    'id' => 1,
                    'key' => 'default-attribute',
                    'value' => 'hola',
                    'attributes' => [
                        [
                            'id' => null,
                            'key' => 'sub-default-attribute',
                            'value' => null,
                            'attributes' => [],
                        ],
                    ],
                ], [
                    'id' => null,
                    'key' => 'another-attribute',
                    'value' => 'por defecto',
                    'attributes' => [],
                ], [
                    'id' => null,
                    'key' => 'another-other-attribute',
                    'value' => 'sin defecto',
                    'attributes' => [],
                ], [
                    'id' => null,
                    'key' => 'multi-attribute',
                    'value' => 'multi-value',
                    'attributes' => [],
                ], [
                    'id' => null,
                    'key' => 'another-multi-attribute',
                    'value' => null,
                    'attributes' => [],
                ],
            ],
            'relations' => [
                'relation-key1' => [
                    [
                        'key' => 'instance-key2',
                        'attributes' => [
                            [
                                'id' => null,
                                'key' => 'one-attribute',
                                'value' => 'hola',
                                'attributes' => [
                                    [
                                        'id' => null,
                                        'key' => 'sub-one-attribute',
                                        'value' => null,
                                        'attributes' => [],
                                    ],
                                ],
                            ],
                        ],
                        'relations' => [],
                    ], [
                        'key' => 'instance-key2',
                        'attributes' => [
                            [
                                'id' => null,
                                'key' => 'one-attribute',
                                'value' => 'hola',
                                'attributes' => [
                                    [
                                        'id' => null,
                                        'key' => 'sub-one-attribute',
                                        'value' => null,
                                        'attributes' => [],
                                    ],
                                ],
                            ],
                        ],
                        'relations' => [],
                    ],
                ],
                'relation-key2' => [
                    [
                        'key' => 'instance-key3',
                        'attributes' => [
                            [
                                'id' => null,
                                'key' => 'one-attribute',
                                'value' => 'hola',
                                'attributes' => [],
                            ],
                        ],
                        'relations' => [
                            'relation-key3' => [
                                [
                                    'key' => 'instance-key4',
                                    'attributes' => [
                                        [
                                            'id' => null,
                                            'key' => 'one-attribute',
                                            'value' => 'adios',
                                            'attributes' => [],
                                        ],
                                    ],
                                    'relations' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $extraction->toArray());
    }

    private function instance(string $key, array $structure, array $attributes, array $relations): Instance
    {
        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages(['es', 'en'])
            ->setStructure($structure)
            ->setClassName('ClassOne')
            ->build();

        return $instance->fill([
            'metadata' => [
                'key' => $key,
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => $attributes,
            'relations' => $relations,
        ]);
    }
}
