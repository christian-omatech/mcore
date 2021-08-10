<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use DateTime;
use DateTimeZone;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Ecore\Editora\Domain\Clazz\Exceptions\InvalidRelationClassException;
use Omatech\Ecore\Editora\Domain\Clazz\Exceptions\InvalidRelationException;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Ecore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Ecore\Editora\Domain\Instance\PublicationStatus;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\Rules\LookupValueOptionException;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\Rules\RequiredValueException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class InstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
                                'required' => true,
                            ],
                        ],
                        'attributes' => [],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceAttributeRequiredNullValue(): void
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
                            'languages' => [
                                '*' => [
                                    'rules' => [
                                        'required' => true
                                    ],
                                ],
                            ],
                        ],
                        'attributes' => [],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        '*' => null
                    ],
                ]
            ],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceAttributeRequiredZeroValue(): void
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
                            'languages' => [
                                '*' => [
                                    'rules' => [
                                        'required' => true
                                    ],
                                ],
                            ],
                        ],
                        'attributes' => [],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        '*' => 0
                    ],
                ]
            ],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceAttributeRequiredEmptyStringValue(): void
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
                            'languages' => [
                                '*' => [
                                    'rules' => [
                                        'required' => true
                                    ],
                                ],
                            ],
                        ],
                        'attributes' => [],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        '*' => ''
                    ],
                ]
            ],
            'relations' => [],
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
                                'required' => false,
                            ],
                        ],
                        'attributes' => [
                            'AnotherAttribute' => [
                                'values' => [
                                    'type' => 'StringValue',
                                    'rules' => [
                                        'required' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test  */
    public function instanceInvalidRelation(): void
    {
        $this->expectException(InvalidRelationException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'relations' => [
                    'relation-key1' => [
                        'class-one',
                    ],
                ],
                'attributes' => [],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [],
            'relations' => [
                'relation-key2' => [
                    'class-two' => [1,2,3],
                ],
            ],
        ]);
    }    
    
    /** @test  */
    public function instanceInvalidRelationClass(): void
    {
        $this->expectException(InvalidRelationClassException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'relations' => [
                    'relation-key1' => [
                        'class-one',
                    ],
                ],
                'attributes' => [],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    'class-two' => [1],
                ],
            ],
        ]);
    }

    /** @test */
    public function instanceFilledCorrectly(): void
    {
        $structure = Yaml::parseFile(dirname(__DIR__, 3).'/Data/data.yml');
        $expected = include dirname(__DIR__, 3).'/Data/ExpectedInstance2.php';

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure($structure[$this->className])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [
                'key' => 'soy-la-key-de-la-instancia',
                'id' => 1,
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
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        'es' => 'hola',
                        'en' => 'adios',
                    ],
                    'attributes' => [
                        'sub-attribute' => [
                            'values' => [
                                'es' => 'hola',
                                'en' => 'adios',
                                'non-existent-language' => 'value',
                            ],
                        ],
                    ],
                ],
                'global-attribute' => [
                    'values' => [
                        'es' => 'hola',
                        'en' => 'adios',
                    ],
                ],
                'specific-attribute' => [
                    'values' => [
                        '+' => 'default',
                        'es' => 'hola',
                        'en' => 'adios',
                    ],
                ],
                'all-languages-attribute' => [
                    'values' => [
                        '*' => 'key1',
                    ],
                ],
                'non-existent-attribute' => [],
            ],
            'relations' => [
                'relation-key1' => [
                    'class-two' => [1,2,3],
                    'class-three' => [4,5,6],
                ],
                'relation-key2' => [
                    'class-four' => [7,8,9],
                    'class-five' => [10,11,12],
                ],
            ],
        ]);

        $this->assertEquals($expected, $instance->toArray());
    }

    /** @test */
    public function instanceLookupInvalidOption(): void
    {
        $this->expectException(LookupValueOptionException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'AllLanguagesAttribute' => [
                        'values' => [
                            'type' => "LookupValue",
                            'languages' => [
                                '*' => [
                                    'configuration' => [
                                        'options' => [
                                            'key1',
                                            'key2'
                                        ],
                                    ],
                                ],

                            ]
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        '*' => 'hola',
                    ],
                ],
            ]
        ]);


    }

    /** @test */
    public function instanceRequiredLookup(): void
    {
        $this->expectException(RequiredValueException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'AllLanguagesAttribute' => [
                        'values' => [
                            'type' => "LookupValue",
                            'languages' => [
                                '*' => [
                                    'configuration' => [
                                        'options' => [
                                            'key1',
                                            'key2'
                                        ],
                                    ],
                                    'rules' => [
                                        'required' => true
                                    ]
                                ],

                            ]
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [],
            'attributes' => []
        ]);


    }
}