<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use DateTime;
use Omatech\Mcore\Editora\Domain\Clazz\Exceptions\InvalidRelationClassException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\InvalidRelationException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidEndDatePublishingException;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Mcore\Editora\Domain\Instance\PublicationStatus;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\InvalidRuleException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\RequiredValueException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueException;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\LookupValueOptionException;
use Symfony\Component\Yaml\Yaml;
use Tests\Data\UniqueValueRepository;

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

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
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
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceLookupInvalidOption(): void
    {
        $this->expectException(LookupValueOptionException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'AllLanguagesAttribute' => [
                        'values' => [
                            'type' => 'LookupValue',
                            'languages' => [
                                '*' => [
                                    'configuration' => [
                                        'options' => [
                                            'key1',
                                            'key2',
                                        ],
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
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'id' => 1,
                            'language' => '*',
                            'value' => 'hola',
                        ],
                    ],
                ],
            ],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceRequiredLookup(): void
    {
        $this->expectException(RequiredValueException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'AllLanguagesAttribute' => [
                        'values' => [
                            'type' => 'LookupValue',
                            'languages' => [
                                '*' => [
                                    'configuration' => [
                                        'options' => [
                                            'key1',
                                            'key2',
                                        ],
                                    ],
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
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function invalidRuleWhenCreateInstance(): void
    {
        $this->expectException(InvalidRuleException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'Invalid' => [
                        'values' => [
                            'rules' => [
                                'noRule' => true,
                            ],
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceSubAttributeValidationFailed(): void
    {
        $this->expectException(RequiredValueException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
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
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test  */
    public function instanceInvalidRelation(): void
    {
        $this->expectException(InvalidRelationException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
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
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [
                'relation-key2' => [
                    1 => 'class-two',
                    2 => 'class-two',
                    3 => 'class-two',
                ],
            ],
        ]);
    }

    /** @test  */
    public function instanceInvalidRelationClass(): void
    {
        $this->expectException(InvalidRelationClassException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
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
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1 => 'class-two',
                ],
            ],
        ]);
    }

    /** @test */
    public function instanceFilledCorrectly(): void
    {
        $structure = Yaml::parseFile(dirname(__DIR__, 3).'/Data/data.yml');
        $expected = include dirname(__DIR__, 3).'/Data/ExpectedInstance2.php';

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
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
                    'startPublishingDate' => '1989-03-08 09:00:00',
                    'endPublishingDate' => '2021-07-27 14:30:00',
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'id' => 1,
                            'language' => 'es',
                            'value' => 'hola',
                            'extraData' => [
                                'ext' => 'png',
                            ],
                        ], [
                            'language' => 'en',
                            'value' => 'adios',
                        ],
                    ],
                    'attributes' => [
                        'sub-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola',
                                ], [
                                    'language' => 'en',
                                    'value' => 'adios',
                                ], [
                                    'language' => 'non-existent-language',
                                    'value' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
                'global-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'hola',
                        ], [
                            'language' => 'en',
                            'value' => 'adios',
                        ],
                    ],
                ],
                'specific-attribute' => [
                    'values' => [
                        [
                            'language' => '+',
                            'value' => 'default',
                        ], [
                            'language' => 'es',
                            'value' => 'hola',
                        ], [
                            'language' => 'en',
                            'value' => 'adios',
                        ],
                    ],
                ],
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'language' => '*',
                            'value' => 'key1',
                        ],
                    ],
                ],
                'non-existent-attribute' => [],
            ],
            'relations' => [
                'relation-key1' => [
                    1 => 'class-two',
                    2 => 'class-two',
                    3 => 'class-two',
                    4 => 'class-three',
                    5 => 'class-three',
                    6 => 'class-three',
                ],
                'relation-key2' => [
                    7 => 'class-four',
                    8 => 'class-four',
                    9 => 'class-four',
                    10 => 'class-five',
                    11 => 'class-five',
                    12 => 'class-five',
                ],
            ],
        ]);

        $this->assertEquals($expected, $instance->toArray());
        $this->assertEquals(1, $instance->id());
        $this->assertEquals([
            'classKey' => 'class-one',
            'key' => 'soy-la-key-de-la-instancia',
            'status' => 'in-revision',
            'startPublishingDate' => new DateTime('1989-03-08 09:00:00'),
            'endPublishingDate' => new DateTime('2021-07-27 14:30:00'),
        ], $instance->data());
        $this->assertIsArray($instance->attributes()->toArray());
        $this->assertEquals([
            [
                'key' => 'relation-key1',
                'instances' => [
                    1 => 'class-two',
                    2 => 'class-two',
                    3 => 'class-two',
                    4 => 'class-three',
                    5 => 'class-three',
                    6 => 'class-three',
                ],
            ], [
                'key' => 'relation-key2',
                'instances' => [
                    7 => 'class-four',
                    8 => 'class-four',
                    9 => 'class-four',
                    10 => 'class-five',
                    11 => 'class-five',
                    12 => 'class-five',
                ],
            ],
        ], $instance->relations()->toArray());
    }

    /** @test  */
    public function instanceInvalidWhenEndDateIsLessThanStartDatePublishing(): void
    {
        $this->expectException(InvalidEndDatePublishingException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'metadata' => [
                    'key' => 'instance',
                    'publication' => [],
                ],
                'attributes' => [],
                'relations' => [],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [
                'key' => 'instance',
                'publication' => [
                    'status' => PublicationStatus::REVISION,
                    'startPublishingDate' => '2022-03-08 09:00:00',
                    'endPublishingDate' => '2021-07-27 14:30:00',
                ],
            ],
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test  */
    public function instanceInvalidWhenEndDateIsEqualThanStartDatePublishing(): void
    {
        $this->expectException(InvalidEndDatePublishingException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'metadata' => [
                    'key' => 'instance',
                    'publication' => [],
                ],
                'attributes' => [],
                'relations' => [],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => [
                'key' => 'instance',
                'publication' => [
                    'status' => PublicationStatus::REVISION,
                    'startPublishingDate' => '2022-03-08 09:00:00',
                    'endPublishingDate' => '2022-03-08 09:00:00',
                ],
            ],
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceUpdateSuccessfully(): void
    {
        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => [],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'id' => 1,
                            'language' => 'es',
                            'value' => 'hola',
                            'extraData' => [
                                'ext' => 'png',
                            ],
                        ], [
                            'id' => null,
                            'language' => 'en',
                            'value' => 'hola',
                            'extraData' => [
                                'ext' => 'jpeg',
                            ],
                        ],
                    ],
                ],
            ],
            'relations' => [],
        ]);

        $this->assertEquals([
            'class' => [
                'key' => 'class-one',
                'relations' => [],
            ],
            'metadata' => [
                'id' => null,
                'key' => 'instance',
                'publication' => [
                    'status' => 'pending',
                    'startPublishingDate' => '1989-03-08 09:00:00',
                    'endPublishingDate' => null,
                ],
            ],
            'attributes' => [
                [
                    'key' => 'default-attribute',
                    'type' => 'string',
                    'values' => [
                        [
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'hola',
                            'extraData' => [
                                'ext' => 'png',
                            ],
                            'id' => 1,
                        ], [
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'hola',
                            'extraData' => [
                                'ext' => 'jpeg',
                            ],
                            'id' => null,
                        ],
                    ],
                    'attributes' => [],
                ],
            ],
            'relations' => [],
        ], $instance->toArray());

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'adios',
                        ], [
                            'language' => 'en',
                            'value' => 'hola',
                        ],
                    ],
                ],
            ],
            'relations' => [],
        ]);

        $this->assertEquals([
            'class' => [
                'key' => 'class-one',
                'relations' => [],
            ],
            'metadata' => [
                'id' => null,
                'key' => 'instance',
                'publication' => [
                    'status' => 'pending',
                    'startPublishingDate' => '1989-03-08 09:00:00',
                    'endPublishingDate' => null,
                ],
            ],
            'attributes' => [
                [
                    'key' => 'default-attribute',
                    'type' => 'string',
                    'values' => [
                        [
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'adios',
                            'extraData' => [
                                'ext' => 'png',
                            ],
                            'id' => 1,
                        ], [
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'hola',
                            'extraData' => [
                                'ext' => 'jpeg',
                            ],
                            'id' => null,
                        ],
                    ],
                    'attributes' => [],
                ],
            ],
            'relations' => [],
        ], $instance->toArray());
    }

    /** @test */
    public function exceptionOnUniqueRuleInInstance(): void
    {
        $this->expectException(UniqueValueException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => [
                        'values' => [
                            'rules' => [
                                'unique' => [
                                    'class' => UniqueValueRepository::class,
                                ],
                            ],
                        ],
                        'attributes' => [
                            'DefaultAttribute' => [],
                        ],
                    ],
                    'DefaultAttribute2' => [
                        'values' => [
                            'rules' => [
                                'unique' => [
                                    'class' => UniqueValueRepository::class,
                                ],
                            ],
                        ],
                        'attributes' => [
                            'DefaultAttribute2' => [
                                'attributes' => [
                                    'DefaultAttribute3' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'hola',
                        ],
                    ],
                    'attributes' => [
                        'default-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'hola',
                                ],
                            ],
                        ],
                    ],
                ],
                'default-attribute2' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'hello',
                        ],
                    ],
                    'attributes' => [
                        'default-attribute2' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'bye',
                                ],
                            ],
                            'attributes' => [
                                'default-attribute3' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'こんにちは',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'relations' => [],
        ]);
    }

    /** @test */
    public function exceptionOnUniqueRuleInDB(): void
    {
        $this->expectException(UniqueValueException::class);

        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute4' => [
                        'values' => [
                            'rules' => [
                                'unique' => [
                                    'class' => UniqueValueRepository::class,
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [
                'default-attribute4' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'hello',
                        ],
                    ],
                ],
            ],
            'relations' => [],
        ]);
    }

    /** @test */
    public function uniqueRuleInInstance(): void
    {
        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => [
                        'values' => [
                            'rules' => [
                                'unique' => [
                                    'class' => UniqueValueRepository::class,
                                ],
                            ],
                        ],
                        'attributes' => [
                            'DefaultAttribute' => [],
                        ],
                    ],
                    'DefaultAttribute2' => [
                        'values' => [
                            'rules' => [
                                'unique' => [
                                    'class' => UniqueValueRepository::class,
                                ],
                            ],
                        ],
                        'attributes' => [
                            'DefaultAttribute2' => [
                                'attributes' => [
                                    'DefaultAttribute3' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'hello',
                        ],
                    ],
                    'attributes' => [
                        'default-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'bye',
                                ],
                            ],
                        ],
                    ],
                ],
                'default-attribute2' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'hello',
                        ],
                    ],
                    'attributes' => [
                        'default-attribute2' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'bye',
                                ],
                            ],
                            'attributes' => [
                                'default-attribute3' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'こんにちは',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'relations' => [],
        ]);
    }

    private function fillMetadataInstance()
    {
        return [
            'key' => 'instance',
            'publication' => [
                'startPublishingDate' => '1989-03-08 09:00:00',
            ],
        ];
    }
}
