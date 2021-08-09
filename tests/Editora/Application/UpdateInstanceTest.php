<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Ecore\Editora\Application\UpdateInstance\UpdateInstanceCommand;
use Omatech\Ecore\Editora\Application\UpdateInstance\UpdateInstanceCommandHandler;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Ecore\Editora\Domain\Instance\Instance;
use PHPUnit\Framework\TestCase;

class UpdateInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function updateInstanceSuccessfully(): void
    {
        $instance = Mockery::mock(Instance::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new UpdateInstanceCommand([
            'metadata' => [
                'id' => 1,
            ],
            'attributes' => [],
            'relations' => [],
        ]);

        $instance->shouldReceive('fill')
            ->with([
                'metadata' => $command->metadata(),
                'attributes' => $command->attributes(),
                'relations' => $command->relations(),
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
