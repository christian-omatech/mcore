<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\ExtractInstance\ExtractInstanceCommand;
use Omatech\Mcore\Editora\Application\ExtractInstance\ExtractInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use PHPUnit\Framework\TestCase;

class ExtractInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function extractInstanceSuccessfully(): void
    {
        $command = new ExtractInstanceCommand('{
            InstanceKey(preview: false, language: es) {
                DefaultAttribute
                RelationKey1(limit:1) {
                    RelationKey2(limit:1)
                }
            }
        }');

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $instance = (new InstanceBuilder())
            ->setLanguages(['es', 'en'])
            ->setStructure([
                'relations' => [
                    'RelationKey1' => [
                        'ClassTwo',
                    ],
                ],
                'attributes' => [
                    'DefaultAttribute' => [],
                ],
            ])
            ->setClassName('ClassOne')
            ->build();

        $instance->fill([
            'metadata' => [
                'id' => 1,
                'key' => 'instance-key',
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
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
                    'attributes' => [],
                ],
            ],
            'relations' => [
                'relation-key1' => [
                    2 => 'class-two',
                ],
            ],
        ]);

        $instance2 = (new InstanceBuilder())
            ->setLanguages(['es', 'en'])
            ->setStructure([
                'relations' => [
                    'RelationKey2' => [
                        'ClassThree',
                    ],
                ],
                'attributes' => [
                    'DefaultAttribute' => [],
                ],
            ])
            ->setClassName('ClassTwo')
            ->build();

        $instance2->fill([
            'metadata' => [
                'id' => 2,
                'key' => 'instance-key2',
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
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
                    'attributes' => [],
                ],
            ],
            'relations' => [
                'relation-key2' => [
                    3 => 'class-three',
                ],
            ],
        ]);

        $instance3 = (new InstanceBuilder())
            ->setLanguages(['es', 'en'])
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => [],
                ],
            ])
            ->setClassName('ClassThree')
            ->build();

        $instance3->fill([
            'metadata' => [
                'id' => 3,
                'key' => 'instance-key3',
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
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
                    'attributes' => [],
                ],
            ],
            'relations' => [],
        ]);

        $repository->shouldReceive('findByKey')
            ->with('instance-key')
            ->andReturn($instance)
            ->once();
        $repository->shouldReceive('findChildrenInstances')
            ->with(1, 'relation-key1', [
                'limit' => 1,
                'language' => 'es',
                'preview' => false,
            ])
            ->andReturn([
                $instance2,
            ])->once();

        $repository->shouldReceive('findChildrenInstances')
            ->with(2, 'relation-key2', [
                'limit' => 1,
                'language' => 'es',
                'preview' => false,
            ])
            ->andReturn([
                $instance3,
            ])->once();

        $extraction = (new ExtractInstanceCommandHandler($repository))->__invoke($command);

        $this->assertEquals([
            'key' => 'instance-key',
            'attributes' => [
                [
                    'id' => null,
                    'key' => 'default-attribute',
                    'value' => 'hola',
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
                                'key' => 'default-attribute',
                                'value' => 'hola',
                                'attributes' => [],
                            ],
                        ],
                        'relations' => [
                            'relation-key2' => [
                                [
                                    'key' => 'instance-key3',
                                    'attributes' => [
                                        [
                                            'id' => null,
                                            'key' => 'default-attribute',
                                            'value' => 'hola',
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
}
