<?php declare(strict_types=1);

namespace Tests\Shared\Utils;

use Omatech\Mcore\Shared\Utils\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    /** @test */
    public function isEmptyWhenEmptyString(): void
    {
        $this->assertTrue(Utils::getInstance()->isEmpty(''));
    }

    /** @test */
    public function notEmptyWhenNotEmptyString(): void
    {
        $this->assertFalse(Utils::getInstance()->isEmpty('test'));
    }

    /** @test */
    public function isEmptyWhenNull(): void
    {
        $this->assertTrue(Utils::getInstance()->isEmpty(null));
    }

    /** @test */
    public function isEmptyWhenEmptyArray(): void
    {
        $this->assertTrue(Utils::getInstance()->isEmpty([]));
    }

    /** @test */
    public function notEmptyWhenArrayItems(): void
    {
        $this->assertFalse(Utils::getInstance()->isEmpty(['test']));
    }

    /** @test */
    public function notEmptyWhenFalse(): void
    {
        $this->assertFalse(Utils::getInstance()->isEmpty(false));
    }

    /** @test */
    public function notEmptyWhenTrue(): void
    {
        $this->assertFalse(Utils::getInstance()->isEmpty(true));
    }

    /** @test */
    public function notEmptyWhenZero(): void
    {
        $this->assertFalse(Utils::getInstance()->isEmpty(0));
    }
}
