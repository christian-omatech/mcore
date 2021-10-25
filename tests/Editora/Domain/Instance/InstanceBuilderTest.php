<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\InvalidValueTypeException;
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

        (new InstanceBuilder($this->mockNeverCalledInstanceCache()))
            ->setClassName($this->className)
            ->setStructure($this->structure[$this->className])
            ->build();
    }

    /** @test */
    public function missingStructureOnInstanceBuilder(): void
    {
        $this->expectException(InvalidStructureException::class);

        (new InstanceBuilder($this->mockNeverCalledInstanceCache()))
            ->setLanguages($this->languages)
            ->setClassName($this->className)
            ->build();
    }

    /** @test */
    public function missingClassNameOnInstanceBuilder(): void
    {
        $this->expectException(InvalidClassNameException::class);

        (new InstanceBuilder($this->mockNeverCalledInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->build();
    }

    /** @test */
    public function invalidValueTypeWhenCreateInstance(): void
    {
        $this->expectException(InvalidValueTypeException::class);

        (new InstanceBuilder($this->mockGetCalledInstanceCache()))
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
        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages($this->languages)
            ->setStructure($this->structure[$this->className])
            ->setClassName($this->className)
            ->build();

        $this->assertEquals($this->expected, $instance->toArray());
    }
}
