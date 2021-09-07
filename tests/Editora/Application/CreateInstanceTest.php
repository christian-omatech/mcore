<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommand;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Events\InstanceHasBeenCreated;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;
use PHPUnit\Framework\TestCase;

class CreateInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function createInstanceCommand(): void
    {
        $command = new CreateInstanceCommand([
            'classKey' => 'test',
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

        $this->assertSame('test', $command->classKey());
        $this->assertSame('test', $command->key());
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

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'key' => 'test1',
            'status' => 'published',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1,
                ],
            ],
        ]);

        $this->assertSame('test', $command->classKey());
        $this->assertSame('test1', $command->key());
        $this->assertSame([
            'key' => 'test1',
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
    public function createInstanceSuccessfully(): void
    {
        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'key' => 'test',
            'status' => 'published',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1,2,3,4,5,6,
                ],
            ],
        ]);

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('exists')->with('test')->andReturn(false)->once();
        $repository->shouldReceive('classKey')->with(1)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(2)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(3)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(4)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(5)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(6)->andReturn('class-two')->once();

        $instance = Mockery::mock(Instance::class);
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
            ->andReturn($instance)
            ->once();

        $eventPublisher = Mockery::mock(EventPublisherInterface::class);
        $eventPublisher->shouldReceive('publish')
            ->with([new InstanceHasBeenCreated($instance)])
            ->andReturn(null)
            ->once();

        $repository->shouldReceive('build')->with($command->classKey())->andReturn($instance)->once();
        $repository->shouldReceive('save')->with($instance)->andReturn(null)->once();

        (new CreateInstanceCommandHandler($eventPublisher, $repository))->__invoke($command);
    }

    /** @test */
    public function recreateExistingInstanceFail(): void
    {
        $this->expectException(InstanceExistsException::class);

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'key' => 'test',
            'status' => 'published',
            'startPublishingDate' => new DateTime('1989-03-08 09:00:00'),
            'attributes' => [],
            'relations' => [],
        ]);

        $eventPublisher = Mockery::mock(EventPublisherInterface::class);
        $eventPublisher->shouldReceive('publish')
            ->andReturn(null)
            ->never();

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('exists')->with('test')->andReturn(true)->once();

        (new CreateInstanceCommandHandler($eventPublisher, $repository))->__invoke($command);
    }

    /** @test */
    public function createInstanceWithInvalidRelation(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'key' => 'test',
            'status' => 'published',
            'startPublishingDate' => new DateTime('1989-03-08 09:00:00'),
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    1,2,3,4,5,6,
                ],
            ],
        ]);

        $eventPublisher = Mockery::mock(EventPublisherInterface::class);
        $eventPublisher->shouldReceive('publish')
            ->andReturn(null)
            ->never();

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('exists')->with('test')->andReturn(false)->once();
        $repository->shouldReceive('classKey')->with(1)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(2)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(3)->andReturn('class-one')->once();
        $repository->shouldReceive('classKey')->with(4)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(5)->andReturn('class-two')->once();
        $repository->shouldReceive('classKey')->with(6)->andReturn(null)->once();

        (new CreateInstanceCommandHandler($eventPublisher, $repository))->__invoke($command);
    }

    /** @test */
    public function createInstanceWithoutRelationsSuccessfully(): void
    {
        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'key' => 'test',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'endPublishingDate' => null,
            'attributes' => [],
        ]);

        $instance = Mockery::mock(Instance::class);
        $instance->shouldReceive('fill')
            ->with([
                'metadata' => $command->metadata(),
                'attributes' => $command->attributes(),
                'relations' => $command->relations(),
            ])
            ->andReturn($instance)
            ->once();

        $event = new InstanceHasBeenCreated($instance);
        $eventPublisher = Mockery::mock(EventPublisherInterface::class);
        $eventPublisher->shouldReceive('publish')
            ->with([$event])
            ->andReturn(null)
            ->once();

        $this->assertSame($event->instance(), $instance);

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('exists')->with('test')->andReturn(false)->once();
        $repository->shouldReceive('build')->with($command->classKey())->andReturn($instance)->once();
        $repository->shouldReceive('save')->with($instance)->andReturn(null)->once();

        (new CreateInstanceCommandHandler($eventPublisher, $repository))->__invoke($command);
    }
}
