<?php

namespace Tests\Editora\Domain\Instance;

use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\Ecore\Editora\Domain\Instance\Exceptions\InvalidValueTypeException;
use Omatech\Ecore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\InvalidRuleConditionException;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\InvalidRuleException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class InstanceBuilderTest extends TestCase
{
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
        (new InstanceBuilder)
            ->setClassName($this->className)
            ->setStructure($this->structure[$this->className])
            ->build();
    }

    /** @test */
    public function missingStructureOnInstanceBuilder(): void
    {
        $this->expectException(InvalidStructureException::class);
        (new InstanceBuilder)
            ->setLanguages($this->languages)
            ->setClassName($this->className)
            ->build();
    }

    /** @test */
    public function missingClassNameOnInstanceBuilder(): void
    {
        $this->expectException(InvalidClassNameException::class);
        (new InstanceBuilder)
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->build();
    }

    /** @test */
    public function invalidRuleWhenCreateInstance(): void
    {
        $this->expectException(InvalidRuleException::class);
        (new InstanceBuilder)
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'Invalid' => [
                        'values' => [
                            'rules' => [
                                'noRule' => true
                            ]
                        ]
                    ]
                ]
            ])
            ->setClassName($this->className)
            ->build();
    }

    /** @test */
    public function invalidValueTypeWhenCreateInstance(): void
    {
        $this->expectException(InvalidValueTypeException::class);
        (new InstanceBuilder)
            ->setLanguages($this->languages)
            ->setStructure([
                'attributes' => [
                    'Invalid' => [
                        'values' => [
                            'type' => 'Invalid'
                        ]
                    ]
                ]
            ])
            ->setClassName($this->className)
            ->build();
    }

    /** @test */
    public function instanceBuildedCorrectly(): void
    {
        $instance = (new InstanceBuilder)
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->setClassName($this->className)
            ->build();
        $this->assertEquals($instance->toArray(), $this->expected);
    }

    /** @test */
    public function instanceBuildedCorrectlyFromReal(): void
    {
        $structure = Yaml::parseFile(dirname(__DIR__, 3).'/Data/dataExample1.yml')['Classes'];
        $className = "SectionActivities";

        $instance = (new InstanceBuilder)
            ->setLanguages($this->languages)
            ->setStructure($structure[$className])
            ->setClassName($className)
            ->build();

        dd($instance->toArray());
    }
}
