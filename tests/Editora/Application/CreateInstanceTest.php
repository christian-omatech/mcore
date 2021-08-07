<?php
namespace Tests\Editora\Application;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Ecore\Editora\Application\CreateInstance\CreateInstanceCommand;
use Omatech\Ecore\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Ecore\Editora\Domain\Instance\Instance;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class CreateInstanceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function createInstanceSuccessfully()
    {
        $instance = Mockery::mock(Instance::class);

        $repository = Mockery::mock(InstanceRepositoryInterface::class);
        $repository->shouldReceive('create')->with(CreateInstanceCommand::class)->andReturn($instance)->once();
        $repository->shouldReceive('save')->with($instance)->andReturn(null)->once();

        (new CreateInstanceCommandHandler($repository))->__invoke(new CreateInstanceCommand([
            'status' => 'pending',
            'className' => 'test',
            'key' => 'key',
            'attributes' => []
        ]));
    }
}
