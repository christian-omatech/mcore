<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\ReadInstance\ReadInstanceCommand;
use Omatech\Mcore\Editora\Application\ReadInstance\ReadInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use PHPUnit\Framework\TestCase;

class ReadInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function readInstanceSuccessfully(): void
    {
        $instance = Mockery::mock(Instance::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new ReadInstanceCommand(1);

        $instance->shouldReceive('toArray')
            ->andReturn([
                'class' => [],
                'metadata' => [
                    'id' => 1,
                ],
                'attributes' => [],
                'relations' => [],
            ])
            ->once();
        $repository->shouldReceive('find')
            ->with($command->id())
            ->andReturn($instance)
            ->once();

        $instanceArray = (new ReadInstanceCommandHandler($repository))->__invoke($command);
        $this->assertSame([
            'class' => [],
            'metadata' => [
                'id' => 1,
            ],
            'attributes' => [],
            'relations' => [],
        ], $instanceArray);
    }

    /** @test */
    public function failedToReadInstance(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);
        $repository = Mockery::mock(InstanceRepositoryInterface::class);

        $command = new ReadInstanceCommand(1);

        $repository->shouldReceive('find')
            ->with($command->id())
            ->andReturn(null)
            ->once();

        (new ReadInstanceCommandHandler($repository))->__invoke($command);
    }
}
