<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\UpdateInstance\UpdateInstanceCommand;
use Omatech\Mcore\Editora\Application\UpdateInstance\UpdateInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use PHPUnit\Framework\TestCase;

class UpdateInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function updateInstanceCommand(): void
    {
        $command = new UpdateInstanceCommand([
            'metadata' => [
                'id' => 1,
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => [],
        ]);

        $this->assertSame([
            'key' => 'test',
            'publication' => [
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => null,
            ],
        ], $command->metadata());
        $this->assertSame([], $command->attributes());
        $this->assertSame([], $command->relations());

        $command = new UpdateInstanceCommand([
            'metadata' => [
                'id' => 1,
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00',
                ],
            ],
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
        $instance = Mockery::mock(Instance::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new UpdateInstanceCommand([
            'metadata' => [
                'id' => 1,
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
        $repository->shouldReceive('find')
            ->with($command->id())
            ->andReturn($instance)
            ->once();
        $repository->shouldReceive('save')
            ->with($instance)
            ->andReturn(null)
            ->once();

        (new UpdateInstanceCommandHandler($repository))->__invoke($command);
    }

    /** @test */
    public function updateInstanceWithInvalidRelation(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new UpdateInstanceCommand([
            'metadata' => [
                'id' => 1,
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
        $repository->shouldReceive('classKey')->with(3)->andReturn(null)->once();
        $repository->shouldReceive('classKey')->with(4)->andReturn('class-two')->never();
        $repository->shouldReceive('classKey')->with(5)->andReturn('class-two')->never();
        $repository->shouldReceive('classKey')->with(6)->andReturn('class-two')->never();

        (new UpdateInstanceCommandHandler($repository))->__invoke($command);
    }

    /** @test */
    public function failedToReadInstance(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new UpdateInstanceCommand([
            'metadata' => [
                'id' => 1,
            ],
            'attributes' => [],
            'relations' => [],
        ]);

        $repository->shouldReceive('find')
            ->with($command->id())
            ->andReturn(null)
            ->once();

        (new UpdateInstanceCommandHandler($repository))->__invoke($command);
    }
}
