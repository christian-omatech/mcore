<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Clazz\Exceptions\InvalidRelationClassException;
use Omatech\Mcore\Editora\Domain\Clazz\Exceptions\InvalidRelationException;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Mcore\Editora\Domain\Instance\PublicationStatus;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\InvalidEndDatePublishingException;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\LookupValueOptionException;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\RequiredValueException;
use Symfony\Component\Yaml\Yaml;

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
                        ], [
                            'id' => 2,
                            'language' => 'en',
                            'value' => 'adios',
                        ],
                    ],
                    'attributes' => [
                        'sub-attribute' => [
                            'values' => [
                                [
                                    'id' => 3,
                                    'language' => 'es',
                                    'value' => 'hola',
                                ], [
                                    'id' => 4,
                                    'language' => 'en',
                                    'value' => 'adios',
                                ], [
                                    'id' => null,
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
                            'id' => 5,
                            'language' => 'es',
                            'value' => 'hola',
                        ], [
                            'id' => 6,
                            'language' => 'en',
                            'value' => 'adios',
                        ],
                    ],
                ],
                'specific-attribute' => [
                    'values' => [
                        [
                            'id' => 7,
                            'language' => '+',
                            'value' => 'default',
                        ], [
                            'id' => 8,
                            'language' => 'es',
                            'value' => 'hola',
                        ], [
                            'id' => 9,
                            'language' => 'en',
                            'value' => 'adios',
                        ],
                    ],
                ],
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'id' => 10,
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
                            'id' => null,
                            'language' => 'es',
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
                            'id' => null,
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'hola',
                        ], [
                            'id' => null,
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => null,
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
                            'id' => 1,
                            'language' => 'es',
                            'value' => 'adios',
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
                            'id' => 1,
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'adios',
                        ], [
                            'id' => null,
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => null,
                        ],
                    ],
                    'attributes' => [],
                ],
            ],
            'relations' => [],
        ], $instance->toArray());
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
