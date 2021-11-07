<?php declare(strict_types=1);

namespace Tests\Editora;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Contracts\ExtractionCacheInterface;
use PHPUnit\Framework\TestCase;

abstract class EditoraTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected array $languages = ['es', 'en'];
    protected string $className = 'VideoGames';
    protected array $structure;

    public function setUp(): void
    {
        $this->structure = (include __DIR__.'/Data/structure.php')['classes'];
    }

    protected function mockNeverCalledInstanceCache(): InstanceCacheInterface
    {
        $instanceCache = (object) Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->never();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();
        return $instanceCache;
    }

    protected function mockGetCalledInstanceCache(): InstanceCacheInterface
    {
        $instanceCache = (object) Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();
        return $instanceCache;
    }

    protected function mockInstanceCache(): InstanceCacheInterface
    {
        $instanceCache = (object) Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();
        return $instanceCache;
    }

    protected function mockExtractionCache(): ExtractionCacheInterface
    {
        $extractionCache = (object) Mockery::mock(ExtractionCacheInterface::class);
        $extractionCache->shouldReceive('get')->andReturn(null)->once();
        $extractionCache->shouldReceive('put')->andReturn(null)->once();
        return $extractionCache;
    }
}
