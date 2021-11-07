<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Application\DeleteInstance\DeleteInstanceCommand;
use Omatech\Mcore\Editora\Application\DeleteInstance\DeleteInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Events\InstanceHasBeenDeleted;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceDoesNotExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;
use PHPUnit\Framework\TestCase;

class DeleteInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function deleteInstanceSuccessfully(): void
    {
        $command = new DeleteInstanceCommand('df86408b-b2e6-4922-83e2-b762b000a335');

        $instance = Mockery::mock(Instance::class);
        $event = new InstanceHasBeenDeleted($instance);
        $eventPublisher = Mockery::mock(EventPublisherInterface::class);
        $eventPublisher->shouldReceive('publish')
            ->with([$event])
            ->andReturn(null)
            ->once();
        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->with($command->uuid())
            ->andReturn($instance)
            ->once();
        $repository->shouldReceive('delete')
            ->with($instance)
            ->andReturn(null)
            ->once();

        $this->assertEquals($event->instance(), $instance);

        (new DeleteInstanceCommandHandler($eventPublisher, $repository))->__invoke($command);
    }

    /** @test */
    public function failedToReadInstance(): void
    {
        $this->expectException(InstanceDoesNotExistsException::class);

        $command = new DeleteInstanceCommand('df86408b-b2e6-4922-83e2-b762b000a335');

        $event = Mockery::mock(EventPublisherInterface::class);
        $event->shouldReceive('publish')
            ->andReturn(null)
            ->never();

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->with($command->uuid())
            ->andReturn(null)
            ->once();

        (new DeleteInstanceCommandHandler($event, $repository))->__invoke($command);
    }
}
