<?php

namespace Tests\Editora\Domain\Instance;

use DateTime;
use DateTimeZone;
use Mockery;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Ecore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Ecore\Editora\Domain\Instance\PublicationStatus;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\Rules\RequiredValueException;
use PHPUnit\Framework\TestCase;

class InstanceTest extends TestCase
{
    private array $languages;
    private string $className = 'ClassOne';

    public function setUp(): void
    {
        $this->languages = ['es', 'en'];
    }

    /** @test */
    public function instanceAttributeValidationFailed(): void
    {
        $this->expectException(RequiredValueException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => [
                        'values' => [
                            'type' => 'StringValue',
                            'rules' => [
                                'required' => true
                            ]
                        ],
                        'attributes' => []
                    ]
                ]
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'publication' => [],
            'attributes' => []
        ]);
    }

    /** @test */
    public function instanceSubAttributeValidationFailed(): void
    {
        $this->expectException(RequiredValueException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => [
                        'values' => [
                            'type' => 'StringValue',
                            'rules' => [
                                'required' => false
                            ]
                        ],
                        'attributes' => [
                            'AnotherAttribute' => [
                                'values' => [
                                    'type' => 'StringValue',
                                    'rules' => [
                                        'required' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'publication' => [],
            'attributes' => []
        ]);
    }

    /** @test */
    public function instanceFilledCorrectly(): void
    {
        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => [
                        'values' => [
                            'type' => 'StringValue',
                            'rules' => [
                                'required' => true
                            ]
                        ],
                        'attributes' => [
                            'AnotherAttribute' => [
                                'values' => [
                                    'type' => 'StringValue',
                                    'rules' => [
                                        'required' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [
                'id' => 1,
                'key' => 'soy-la-key-de-la-instancia',
                'publication' => [
                    'status' => PublicationStatus::REVISION,
                    'startPublishingDate' => DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        '1989-03-08 09:00:00',
                        new DateTimeZone('Europe/Madrid')
                    ),
                    'endPublishingDate' => DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        '2021-07-27 14:30:00',
                        new DateTimeZone('Europe/Madrid')
                    ),
                ]
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        'es' => 'hola',
                        'en' => 'adios'
                    ],
                    'attributes' => [
                        'another-attribute' => [
                            'values' => [
                                'es' => 'hola',
                                'en' => 'adios',
                                'non-existent-language' => 'value'
                            ]
                        ]
                    ]
                ],
                'non-existent-attribute' => []
            ]
        ]);

        $this->assertEquals([
            'metadata' => [
                'name' => 'class-one',
                'caption' => 'class.class-one',
                'relations' => [],
                'id' => 1,
                'key' => 'soy-la-key-de-la-instancia',
                'publication' => [
                    'status' => 'in-revision',
                    'startPublishingDate' => '08/03/1989 09:00:00',
                    'endPublishingDate' => '27/07/2021 14:30:00'
                ]
            ],
            'attributes' => [
                [
                    'metadata' => [
                        'id' => null,
                        'key' => 'default-attribute',
                    ],
                    'component' => [
                        'type' => 'string',
                        'caption' => 'attribute.default-attribute.string',
                    ],
                    'values' => [
                        [
                            'language' => 'es',
                            'rules' => [
                                'required' => true
                            ],
                            'configuration' => [],
                            'value' => 'hola'
                        ],
                        [
                            'language' => 'en',
                            'rules' => [
                                'required' => true
                            ],
                            'configuration' => [],
                            'value' => 'adios'
                        ]
                    ],
                    'attributes' => [
                        [
                            'metadata' => [
                                'id' => null,
                                'key' => 'another-attribute',
                            ],
                            'component' => [
                                'type' => 'string',
                                'caption' => 'attribute.another-attribute.string',
                            ],
                            'values' => [
                                [
                                    'language' => 'es',
                                    'rules' => [
                                        'required' => true
                                    ],
                                    'configuration' => [],
                                    'value' => 'hola'
                                ],
                                [
                                    'language' => 'en',
                                    'rules' => [
                                        'required' => true
                                    ],
                                    'configuration' => [],
                                    'value' => 'adios'
                                ]
                            ],
                            'attributes' => []
                        ]
                    ]
                ]
            ]
        ], $instance->toArray());
    }
}
