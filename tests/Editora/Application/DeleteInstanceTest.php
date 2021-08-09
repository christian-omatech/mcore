<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Ecore\Editora\Application\DeleteInstance\DeleteInstanceCommand;
use Omatech\Ecore\Editora\Application\DeleteInstance\DeleteInstanceCommandHandler;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Ecore\Editora\Domain\Instance\Instance;
use PHPUnit\Framework\TestCase;

class DeleteInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function deleteInstanceSuccessfully(): void
    {
        $instance = Mockery::mock(Instance::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new DeleteInstanceCommand(1);

        $repository->shouldReceive('find')
            ->with($command->id())
            ->andReturn($instance)
            ->once();
        $repository->shouldReceive('delete')
            ->with($instance)
            ->andReturn(null)
            ->once();

        (new DeleteInstanceCommandHandler($repository))->__invoke($command);
    }

    /** @test */
    public function failedToReadInstance(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new DeleteInstanceCommand(1);

        $repository->shouldReceive('find')
            ->with($command->id())
            ->andReturn(null)
            ->once();

        (new DeleteInstanceCommandHandler($repository))->__invoke($command);
    }
}
