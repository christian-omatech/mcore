<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;

class InstanceExtractionTest extends TestCase
{
    /** @test */
    public function extractInstanceWithQueryExtraction()
    {
        $query = '{
            InstanceKey(preview: false, language: es) {
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
        $query = (new QueryParser)->parse($query);

        $instance = $this->instance('instance-key1', [
            'attributes' => [
                'DefaultAttribute' => [
                    'attributes' => [
                        'SubDefaultAttribute' => []
                    ]
                ],
                'AnotherAttribute' => [
                    'values' => [
                        'languages' => [
                            '+' => []
                        ]
                    ]
                ],
                'AnotherOtherAttribute' => [
                    'values' => [
                        'languages' => [
                            '+' => []
                        ]
                    ]
                ],
                'MultiAttribute' => [
                    'values' => [
                        'languages' => [
                            '*' => []
                        ]
                    ]
                ],
                'AnotherMultiAttribute' => [
                    'values' => [
                        'languages' => [
                            '*' => []
                        ]
                    ]
                ],
                'NonQueryAttribute' => []
            ]
        ], [
            'default-attribute' => [
                'values' => [
                    [
                        'language' => 'es',
                        'value' => 'hola',
                    ], [
                        'language' => 'en',
                        'value' => 'hello',
                    ],
                ]
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
                        'value' => 'por defecto'
                    ]
                ]
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
                        'value' => 'por defecto'
                    ]
                ]
            ],
            'multi-attribute' => [
                'values' => [
                    [
                        'language' => '*',
                        'value' => 'multi-value'
                    ]
                ]
            ],
            'another-multi-attribute' => [
                'values' => [
                    [
                        'language' => '*',
                        'value' => null
                    ]
                ]
            ]
        ], []);
        $relations = [
            'relation-key1' => [
                'instances' => [
                    $this->instance('instance-key2', [
                        'attributes' => [
                            'OneAttribute' => [
                                'attributes' => [
                                    'SubOneAttribute' => []
                                ]
                            ]
                        ]
                    ], [
                        'one-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola'
                                ], [
                                    'language' => 'en',
                                    'value' => 'hello'
                                ]
                            ],
                            'attributes' => []
                        ]
                    ], []),
                    $this->instance('instance-key2', [
                        'attributes' => [
                            'OneAttribute' => [
                                'attributes' => [
                                    'SubOneAttribute' => []
                                ]
                            ]
                        ]
                    ], [
                        'one-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola'
                                ], [
                                    'language' => 'en',
                                    'value' => 'hello'
                                ]
                            ],
                            'attributes' => []
                        ]
                    ], [])
                ],
                'relations' => []
            ],
            'relation-key2' => [
                'instances' => [
                    $this->instance('instance-key3', [
                        'relations' => [
                            'RelationKey3' => [
                                'ClassOne'
                            ]
                        ],
                        'attributes' => [
                            'OneAttribute' => []
                        ]
                    ], [
                        'one-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola'
                                ], [
                                    'language' => 'en',
                                    'value' => 'hello'
                                ]
                            ],
                            'attributes' => []
                        ]
                    ], [
                        'relation-key3' => [
                            1 => 'class-one',
                        ],
                    ])
                ],
                'relations' => [
                    'relation-key3' => [
                        'instances' => [
                            $this->instance('instance-key4', [
                                'attributes' => [
                                    'OneAttribute' => []
                                ]
                            ], [
                                'one-attribute' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'adios'
                                        ], [
                                            'language' => 'en',
                                            'value' => 'bye'
                                        ]
                                    ],
                                    'attributes' => []
                                ]
                            ], [])
                        ],
                        'relations' => []
                    ]
                ]
            ]
        ];
        $query->addRelations($relations);
        $extractor = new Extractor($query, $instance, $relations);
        $extraction = $extractor->extract();

        $this->assertEquals([
            'key' => 'instance-key',
            'language' => 'es',
            'attributes' => [
                [
                    'key' => 'default-attribute',
                    'value' => 'hola',
                    'attributes' => [
                        [
                            'key' => 'sub-default-attribute',
                            'value' => null,
                            'attributes' => []
                        ]
                    ]
                ], [
                    'key' => 'another-attribute',
                    'value' => 'por defecto',
                    'attributes' => []
                ], [
                    'key' => 'another-other-attribute',
                    'value' => 'sin defecto',
                    'attributes' => []
                ], [
                    'key' => 'multi-attribute',
                    'value' => 'multi-value',
                    'attributes' => []
                ], [
                    'key' => 'another-multi-attribute',
                    'value' => null,
                    'attributes' => []
                ]
            ],
            'params' => [
                'preview' => false,
                'language' => 'es'
            ],
            'relations' => [
                'relation-key1' => [
                    [
                        'key' => 'instance-key2',
                        'language' => 'es',
                        'params' => [
                            'preview' => false,
                            'language' => 'es'
                        ],
                        'attributes' => [
                            [
                                'key' => 'one-attribute',
                                'value' => 'hola',
                                'attributes' => [
                                    [
                                        'key' => 'sub-one-attribute',
                                        'value' => null,
                                        'attributes' => []
                                    ]
                                ]
                            ]
                        ],
                        'relations' => []
                    ], [
                        'key' => 'instance-key2',
                        'language' => 'es',
                        'params' => [
                            'preview' => false,
                            'language' => 'es'
                        ],
                        'attributes' => [
                            [
                                'key' => 'one-attribute',
                                'value' => 'hola',
                                'attributes' => [
                                    [
                                        'key' => 'sub-one-attribute',
                                        'value' => null,
                                        'attributes' => []
                                    ]
                                ]
                            ]
                        ],
                        'relations' => []
                    ]
                ],
                'relation-key2' => [
                    [
                        'key' => 'instance-key3',
                        'language' => 'es',
                        'params' => [
                            'preview' => false,
                            'language' => 'es'
                        ],
                        'attributes' => [
                            [
                                'key' => 'one-attribute',
                                'value' => 'hola',
                                'attributes' => []
                            ]
                        ],
                        'relations' => [
                            'relation-key3' => [
                                [
                                    'key' => 'instance-key4',
                                    'language' => 'es',
                                    'params' => [
                                        'preview' => false,
                                        'language' => 'es'
                                    ],
                                    'attributes' => [
                                        [
                                            'key' => 'one-attribute',
                                            'value' => 'adios',
                                            'attributes' => []
                                        ]
                                    ],
                                    'relations' => []
                                ]
                            ]
                        ]
                    ]
                ]
            ]
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
