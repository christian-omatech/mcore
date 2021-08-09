<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Ecore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidValueTypeException;
use Omatech\Ecore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Ecore\Editora\Domain\Instance\InstanceCache;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\InvalidRuleException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Yaml\Yaml;

class InstanceBuilderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private array $structure;
    private array $languages;
    private string $className = 'ClassOne';
    private array $expected;

    public function setUp(): void
    {
        $this->languages = ['es', 'en'];
        $this->structure = Yaml::parseFile(dirname(__DIR__, 3).'/Data/data.yml');
        $this->expected = include dirname(__DIR__, 3).'/Data/ExpectedInstance.php';
    }

    /** @test */
    public function missingLanguagesOnInstanceBuilder(): void
    {
        $this->expectException(InvalidLanguagesException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->never();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();

        (new InstanceBuilder($instanceCache))
            ->setClassName($this->className)
            ->setStructure($this->structure[$this->className])
            ->build();
    }

    /** @test */
    public function missingStructureOnInstanceBuilder(): void
    {
        $this->expectException(InvalidStructureException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->never();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();

        (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setClassName($this->className)
            ->build();
    }

    /** @test */
    public function missingClassNameOnInstanceBuilder(): void
    {
        $this->expectException(InvalidClassNameException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->never();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();

        (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->build();
    }

    /** @test */
    public function invalidRuleWhenCreateInstance(): void
    {
        $this->expectException(InvalidRuleException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();

        (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'Invalid' => [
                        'values' => [
                            'rules' => [
                                'noRule' => true,
                            ],
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();
    }

    /** @test */
    public function invalidValueTypeWhenCreateInstance(): void
    {
        $this->expectException(InvalidValueTypeException::class);

        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();

        (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'Invalid' => [
                        'values' => [
                            'type' => 'Invalid',
                        ],
                    ],
                ],
            ])
            ->setClassName($this->className)
            ->build();
    }

    /** @test */
    public function instanceBuildedCorrectly(): void
    {
        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();

        $instance = (new InstanceBuilder($instanceCache))
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->setClassName($this->className)
            ->build();

        $this->assertEquals($instance->toArray(), $this->expected);
    }

    /** @test */
    public function instanceBuildedWithLocalCache(): void
    {
        $cache = InstanceCache::getInstance();
        $instance1 = (new InstanceBuilder($cache))
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->setClassName($this->className)
            ->build();

        $instance2 = (new InstanceBuilder($cache))
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->setClassName($this->className)
            ->build();

        $this->assertNotSame($instance1, $instance2);
        (new ReflectionClass($cache))->setStaticPropertyValue('instance', null);
    }

    /** @test */
    public function instanceBuildedCorrectlyFromReal(): void
    {
        $structure = Yaml::parseFile(dirname(__DIR__, 3).'/Data/dataExample1.yml')['Classes'];

        foreach ($structure as $className => $classStructure) {
            (new InstanceBuilder())
                ->setLanguages($this->languages)
                ->setStructure($classStructure)
                ->setClassName($className)
                ->build();
        }
    }
}
