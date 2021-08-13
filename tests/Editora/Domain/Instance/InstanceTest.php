<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use DateTime;
use DateTimeZone;
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
                        '*' => 'hola',
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
