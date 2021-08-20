<?php

namespace Tests\Editora\Domain\Value\Rules;

use Mockery;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\UniqueValueException;
use Omatech\Mcore\Editora\Domain\Value\Rules\Contracts\UniqueValueInterface;
use Omatech\Mcore\Editora\Domain\Value\Rules\Unique;
use PHPUnit\Framework\TestCase;

class UniqueTest extends TestCase
{
    /** @test */
    public function validateUniqueRule(): void
    {
        $this->expectException(UniqueValueException::class);
        $mock = Mockery::mock(UniqueValueInterface::class);
        $mock->shouldReceive('unique')->andReturn(true)->once();

        $rule = new Unique(null, $mock);
        $rule->validate('attribute', 'es', 'value');
    }
}
