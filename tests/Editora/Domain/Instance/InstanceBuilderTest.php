<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\InvalidValueTypeException;
use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceBuilder;
use Tests\Editora\EditoraTestCase;
use Tests\Editora\Data\UniqueValueRepository;

class InstanceBuilderTest extends EditoraTestCase
{
    /** @test */
    public function expectInvalidLanguageExceptionGivenEmptyLanguages(): void
    {
        $this->expectException(InvalidLanguagesException::class);

        (new InstanceBuilder($this->mockNeverCalledInstanceCache()))
            ->setLanguages([])
            ->build();
    }

    /** @test */
    public function expectInvalidStructureExceptionGivenEmptyStructure(): void
    {
        $this->expectException(InvalidStructureException::class);

        (new InstanceBuilder($this->mockNeverCalledInstanceCache()))
            ->setStructure([])
            ->build();
    }

    /** @test */
    public function expectInvalidClassNameExceptionGivenBlankClassName(): void
    {
        $this->expectException(InvalidClassNameException::class);

        (new InstanceBuilder($this->mockNeverCalledInstanceCache()))
            ->setClassName('')
            ->build();
    }

    /** @test */
    public function expectInvalidValueTypeExceptionGivenANonExistingValueType(): void
    {
        $this->expectException(InvalidValueTypeException::class);

        (new InstanceBuilder($this->mockGetCalledInstanceCache()))
            ->setStructure([
                'attributes' => [
                    'Invalid' => [
                        'values' => [
                            'type' => 'Invalid',
                        ],
                    ],
                ],
            ])
            ->build();
    }

    /** @test */
    public function expectInstanceBuiltCorrectly(): void
    {
        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->build();

        $instanceArray = (new InstanceArrayBuilder())
            ->addClassKey('video-games')
            ->addClassRelations('platforms', ['platform'])
            ->addClassRelations('reviews', ['articles', 'blogs'])
            ->addPublication('pending')
            ->addAttribute('title', 'string', [], [
                fn($builder) => $builder->addSubAttribute('code', 'string', []),
                fn($builder) => $builder->addSubAttribute('sub-title', 'string', [
                    ['language' => 'es', 'rules' => ['required' => true]],
                    ['language' => 'en', 'rules' => ['required' => true]]
                ]),
            ])
            ->addAttribute('sub-title', 'string', [
                ['language' => 'es', 'rules' => ['uniqueDB' => ['class' => UniqueValueRepository::class]]],
                ['language' => 'en', 'rules' => ['uniqueDB' => ['class' => UniqueValueRepository::class]]]
            ])
            ->addAttribute('synopsis', 'text', [
                ['language' => 'es', 'rules' => ['required' => true], 'configuration' => ['cols' => 10, 'rows' => 10]],
                ['language' => 'en', 'rules' => ['required' => true], 'configuration' => ['cols' => 10, 'rows' => 10]]
            ])
            ->addAttribute('release-date', 'string', [
                ['language' => 'es', 'rules' => ['required' => true], 'configuration' => ['cols' => 10, 'rows' => 10]],
                ['language' => 'en', 'rules' => ['required' => false], 'configuration' => ['cols' => 20, 'rows' => 20]],
                ['language' => '+', 'rules' => ['required' => true], 'configuration' => ['cols' => 30, 'rows' => 30]],
            ])
            ->addAttribute('code', 'lookup', [
                [
                    'language' => '*', 'rules' => ['required' => true, 'unique' => []],
                    'configuration' => ['options' => ['pc-code', 'playstation-code', 'xbox-code', 'switch-code']]
                ]
            ])
            ->build();

        $this->assertEquals($instanceArray, $instance->toArray());
    }
}
