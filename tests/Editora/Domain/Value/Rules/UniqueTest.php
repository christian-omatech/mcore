<?php

namespace Tests\Editora\Domain\Value\Rules;

use Mockery;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\UniqueValueException;
use Omatech\Mcore\Editora\Domain\Value\Rules\Contracts\UniqueValueInterface;
use Omatech\Mcore\Editora\Domain\Value\Rules\Unique;
use Tests\Data\UniqueValueRepository;
use Tests\Editora\Domain\Instance\TestCase;

class UniqueTest extends TestCase
{
    /** @test */
    public function validateUniqueRule(): void
    {
        $this->expectException(UniqueValueException::class);
        $unique = new Unique([
            'class' => UniqueValueRepository::class,
            'somecondition' => ''
        ]);
        $this->assertEquals(['somecondition' => ''], $unique->condition());
        $unique->validate('attr', 'es', 'hola');
    }
}
