<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    use MockeryPHPUnitIntegration;

    protected function mockNeverCalledInstanceCache(): InstanceCacheInterface
    {
        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->never();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();
        return $instanceCache;
    }

    protected function mockGetCalledInstanceCache(): InstanceCacheInterface
    {
        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->never();
        return $instanceCache;
    }

    protected function mockInstanceCache(): InstanceCacheInterface
    {
        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();
        return $instanceCache;
    }
}
