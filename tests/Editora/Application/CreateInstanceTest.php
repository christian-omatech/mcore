<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommand;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use PHPUnit\Framework\TestCase;

class CreateInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function createInstanceCommand(): void
    {
        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'metadata' => [
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00'
                ]
            ],
            'attributes' => [],
        ]);

        $this->assertSame('test', $command->classKey());
        $this->assertSame([
            'key' => 'test',
            'publication' => [
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => null
            ]
        ], $command->metadata());
        $this->assertSame([], $command->attributes());
        $this->assertSame([], $command->relations());

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'metadata' => [
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00'
                ]
            ],
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1,
                ],
            ],
        ]);

        $this->assertSame('test', $command->classKey());
        $this->assertSame([
            'key' => 'test',
            'publication' => [
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => null
            ]
        ], $command->metadata());
        $this->assertSame([], $command->attributes());
        $this->assertSame([
            'relation-key1' => [
                1,
            ],
        ], $command->relations());
    }

    /** @test */
    public function createInstanceSuccessfully(): void
    {
        $instance = Mockery::mock(Instance::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'metadata' => [
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1,2,3,4,5,6,
                ],
            ],
        ]);

        $repository->shouldReceive('classKey')->with(1)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(2)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(3)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(4)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(5)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(6)->andReturn('class-two')->once();

        $instance->shouldReceive('fill')
            ->with([
                'metadata' => $command->metadata(),
                'attributes' => $command->attributes(),
                'relations' => [
                    'relation-key1' => [
                        1 => 'class-one',
                        2 => 'class-one',
                        3 => 'class-one',
                        4 => 'class-two',
                        5 => 'class-two',
                        6 => 'class-two',
                    ],
                ],
            ])
            ->andReturn(null)
            ->once();
        $repository->shouldReceive('build')
            ->with($command->classKey())
            ->andReturn($instance)
            ->once();
        $repository->shouldReceive('save')
            ->with($instance)
            ->andReturn(null)
            ->once();

        (new CreateInstanceCommandHandler($repository))->__invoke($command);
    }

    /** @test */
    public function createInstanceWithInvalidRelation(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'metadata' => [
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => new DateTime('1989-03-08 09:00:00'),
                ],
            ],
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1,2,3,4,5,6,
                ],
            ],
        ]);

        $repository->shouldReceive('classKey')->with(1)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(2)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(3)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(4)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(5)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(6)->andReturn(null)->once();

        (new CreateInstanceCommandHandler($repository))->__invoke($command);
    }

    /** @test */
    public function createInstanceWithoutRelationsSuccessfully(): void
    {
        $instance = Mockery::mock(Instance::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'metadata' => [
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00',
                    'end_publishing_date' => null
                ],
            ],
            'attributes' => [],
        ]);

        $instance->shouldReceive('fill')
            ->with([
                'metadata' => $command->metadata(),
                'attributes' => $command->attributes(),
                'relations' => $command->relations(),
            ])
            ->andReturn(null)
            ->once();
        $repository->shouldReceive('build')
            ->with($command->classKey())
            ->andReturn($instance)
            ->once();
        $repository->shouldReceive('save')
            ->with($instance)
            ->andReturn(null)
            ->once();

        (new CreateInstanceCommandHandler($repository))->__invoke($command);
    }
}
