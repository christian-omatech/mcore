<?php
//namespace Tests\Editora\Application;
//
//use Mockery;
//use PHPUnit\Framework\TestCase;
//use Symfony\Component\Yaml\Yaml;
//use Omatech\Ecore\Editora\Domain\Instance\InstanceBuilder;
//use Omatech\Ecore\Editora\Application\CreateInstance\CreateInstanceCommand;
//use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
//use Omatech\Ecore\Editora\Application\CreateInstance\CreateInstanceCommandHandler;
//
//class CreateInstanceTest extends TestCase
//{
//    private array $structure;
//    private array $languages;
//    private string $className = 'ClassOne';
//
//    public function setUp(): void
//    {
//        $this->languages = ['es', 'en'];
//        $this->structure = Yaml::parseFile(__DIR__ . '/../../Data/data.yml');
//    }
//
//    /** @test */
//    public function createInstanceSuccessfully()
//    {
//        $instance = (new InstanceBuilder)
//            ->setLanguages($this->languages)
//            ->setStructure($this->structure[$this->className])
//            ->setClassName($this->className)
//            ->build();
//
//        $repository = Mockery::mock(InstanceRepositoryInterface::class);
//        $repository->shouldReceive('create')
//            ->with(CreateInstanceCommand::class)
//            ->andReturn($instance);
//        $repository->shouldReceive('save')
//            ->with($instance)
//            ->andReturnNull();
//
//        $handler = (new CreateInstanceCommandHandler($repository))
//            ->__invoke(new CreateInstanceCommand([
//                'status' => 'pending',
//                'className' => 'test',
//                'key' => 'key',
//                'attributes' => []
//            ]));
//        $this->assertNull($handler);
//    }
//}
