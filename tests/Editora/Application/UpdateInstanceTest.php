<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\UpdateInstance\UpdateInstanceCommand;
use Omatech\Mcore\Editora\Application\UpdateInstance\UpdateInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Events\InstanceHasBeenUpdated;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;
use PHPUnit\Framework\TestCase;

class UpdateInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function updateInstanceCommand(): void
    {
        $command = new UpdateInstanceCommand([
            'uuid' => '1',
            'key' => 'test',
            'status' => 'published',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'attribute-1' => [
                    'values' => [
                        [
                            'value' => 'value-1',
                            'language' => 'es',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            'key' => 'test',
            'publication' => [
                'status' => 'published',
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => null,
            ],
        ], $command->metadata());
        $this->assertSame([
            'attribute-1' => [
                'values' => [
                    [
                        'value' => 'value-1',
                        'language' => 'es',
                    ],
                ],
            ],
        ], $command->attributes());
        $this->assertSame([], $command->relations());

        $command = new UpdateInstanceCommand([
            'uuid' => '1',
            'key' => 'test',
            'status' => 'published',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1,
                ],
            ],
        ]);

        $this->assertSame([
            'key' => 'test',
            'publication' => [
                'status' => 'published',
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => null,
            ],
        ], $command->metadata());
        $this->assertSame([], $command->attributes());
        $this->assertSame([
            'relation-key1' => [
                1,
            ],
        ], $command->relations());
    }

    /** @test */
    public function updateInstanceSuccessfully(): void
    {
        $command = new UpdateInstanceCommand([
            'uuid' => '1',
            'key' => 'test',
            'status' => 'published',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    '1','2','3','4','5','6',
                ],
            ],
        ]);

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('classKey')->with('1')->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with('2')->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with('3')->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with('4')->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with('5')->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with('6')->andReturn('class-two')->once();

        $instance = Mockery::mock(Instance::class);
        $instance2 = Mockery::mock(Instance::class);
        $instance->shouldReceive('fill')
            ->with([
                'metadata' => $command->metadata(),
                'attributes' => $command->attributes(),
                'relations' => [
                    'relation-key1' => [
                        '1' => 'class-one',
                        '2' => 'class-one',
                        '3' => 'class-one',
                        '4' => 'class-two',
                        '5' => 'class-two',
                        '6' => 'class-two',
                    ],
                ],
            ])
            ->andReturn($instance)
            ->once();

        $repository->shouldReceive('find')
            ->with($command->uuid())
            ->andReturn($instance)
            ->once();
        $repository->shouldReceive('save')
            ->with($instance)
            ->andReturn(null)
            ->once();

        $repository->shouldReceive('clone')
            ->with($instance)
            ->andReturn($instance2)
            ->once();

        $event = new InstanceHasBeenUpdated($instance2, $instance);
        $eventPublisher = Mockery::mock(EventPublisherInterface::class);
        $eventPublisher->shouldReceive('publish')
            ->with([$event])
            ->andReturn(null)
            ->once();

        $this->assertNotSame($event->old(), $event->new());

        (new UpdateInstanceCommandHandler($eventPublisher, $repository))->__invoke($command);
    }

    /** @test */
    public function updateInstanceWithInvalidRelation(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);

        $command = new UpdateInstanceCommand([
            'uuid' => '1',
            'key' => 'test',
            'status' => 'published',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    '1','2','3','4','5','6',
                ],
            ],
        ]);

        $event = Mockery::mock(EventPublisherInterface::class);
        $event->shouldReceive('publish')
            ->andReturn(null)
            ->never();

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('classKey')->with('1')->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with('2')->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with('3')->andReturn(null)->once();
        $repository->shouldReceive('classKey')->with('4')->andReturn('class-two')->never();
        $repository->shouldReceive('classKey')->with('5')->andReturn('class-two')->never();
        $repository->shouldReceive('classKey')->with('6')->andReturn('class-two')->never();

        (new UpdateInstanceCommandHandler($event, $repository))->__invoke($command);
    }

    /** @test */
    public function failedToReadInstance(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);

        $command = new UpdateInstanceCommand([
            'uuid' => '1',
            'key' => 'test',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [],
            'relations' => [],
        ]);

        $event = Mockery::mock(EventPublisherInterface::class);
        $event->shouldReceive('publish')
            ->andReturn(null)
            ->never();

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->with($command->uuid())
            ->andReturn(null)
            ->once();

        (new UpdateInstanceCommandHandler($event, $repository))->__invoke($command);
    }
}
