<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommand;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use PHPUnit\Framework\TestCase;

class CreateInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function createInstanceSuccessfully(): void
    {
        $instance = Mockery::mock(Instance::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new CreateInstanceCommand([
            'classKey' => 'test',
            'metadata' => [],
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
