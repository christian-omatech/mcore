<?php

namespace Tests\Editora\Domain\Instance;

use DateTime;
use DateTimeZone;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Ecore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Ecore\Editora\Domain\Instance\PublicationStatus;
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
                ]
                ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        'es' => 'hola',
                        'en' => 'adios'
                    ],
                    'attributes' => [
                        'sub-attribute' => [
                            'values' => [
                                'es' => 'hola',
                                'en' => 'adios',
                                'non-existent-language' => 'value'
                            ]
                        ]
                    ]
                ],
                'global-attribute' => [
                    'values' => [
                        'es' => 'hola',
                        'en' => 'adios'
                    ],
                ],
                'specific-attribute' => [
                    'values' => [
                        '+' => 'default',
                        'es' => 'hola',
                        'en' => 'adios'
                    ],
                ],
                'all-languages-attribute' => [
                    'values' => [
                        '*' => 'hola'
                    ],
                ],
                'non-existent-attribute' => []
            ]
        ]);

        $this->assertEquals($expected, $instance->toArray());
    }
}
