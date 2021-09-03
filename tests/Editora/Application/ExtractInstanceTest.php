<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\ExtractInstance\ExtractInstanceCommand;
use Omatech\Mcore\Editora\Application\ExtractInstance\ExtractInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use PHPUnit\Framework\TestCase;

class ExtractionInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function extractInstanceSuccessfully(): void
    {
        $command = new ExtractInstanceCommand('{
            InstanceKey(preview: false, language: es) {
                DefaultAttribute
            }
        }');

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $instance = (new InstanceBuilder())
            ->setLanguages(['es', 'en'])
            ->setStructure([
                'attributes' => [
                    'DefaultAttribute' => []
                ]
            ])
            ->setClassName('ClassOne')
            ->build();

        $instance->fill([
            'metadata' => [
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
                        ]
                    ],
                    'attributes' => []
                ]
            ],
            'relations' => [],
        ]);

        $repository->shouldReceive('findByKey')
            ->with('instance-key')
            ->andReturn($instance)
            ->once();

        $extraction = (new ExtractInstanceCommandHandler($repository))->__invoke($command);

        $this->assertEquals([
            'key' => 'instance-key',
            'language' => 'es',
            'attributes' => [
                [
                    'key' => 'default-attribute',
                    'value' => 'hola',
                    'attributes' => []
                ]
            ],
            'params' => [
                'preview' => false,
                'language' => 'es'
            ],
            'relations' => []
        ], $extraction->toArray());
    }
}
