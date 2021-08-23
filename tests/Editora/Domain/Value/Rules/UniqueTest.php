<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Value\Rules;

use Tests\Editora\Domain\Instance\TestCase;

class UniqueTest extends TestCase
{
    /** @test */
    public function validateUniqueRule(): void
    {
        // $this->expectException(UniqueValueException::class);
        // $value = new Value('test', 'es', [
        //     'rules' => [],
        //     'configuration' => [],
        // ]);
        // $value->fill('testValue');
        // $unique = new Unique(new AttributeCollection([]), [
        //     'class' => UniqueValueRepository::class,
        // ]);
        // $unique->validate($value);
    }
}
