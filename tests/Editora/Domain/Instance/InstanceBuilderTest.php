<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\InvalidValueTypeException;

class InstanceBuilderTest extends TestCase
{
    private array $languages = ['es', 'en'];
    private string $className = 'VideoGames';
    private array $structure;

    public function setUp(): void
    {
        $this->structure = (include dirname(__DIR__, 3).'/Data/data.php')['classes'];
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

        $this->assertEquals([
            "class" => [
                "key" => "video-games",
                "relations" => [
                    [
                        "key" => "platforms",
                        "classes" => [
                            "platform"
                        ]
                    ], [
                        "key" => "reviews",
                        "classes" => [
                            "articles",
                            "blogs"
                        ]
                    ]
                ]
            ],
            "metadata" => [
                "uuid" => null,
                "key" => "",
                "publication" => [
                    "status" => "pending",
                    "startPublishingDate" => null,
                    "endPublishingDate" => null
                ]
            ],
            "attributes" => [
                [
                    "key" => "title",
                    "type" => "string",
                    "values" => [
                        [
                            "uuid" => null,
                            "language" => "es",
                            "rules" => [],
                            "configuration" => [],
                            "value" => null,
                            "extraData" => []
                        ], [
                            "uuid" => null,
                            "language" => "en",
                            "rules" => [],
                            "configuration" => [],
                            "value" => null,
                            "extraData" => []
                        ]
                    ],
                    "attributes" => [
                        [
                            "key" => "sub-title",
                            "type" => "string",
                            "values" => [
                                [
                                    "uuid" => null,
                                    "language" => "es",
                                    "rules" => [],
                                    "configuration" => [],
                                    "value" => null,
                                    "extraData" => []
                                ], [
                                    "uuid" => null,
                                    "language" => "en",
                                    "rules" => [],
                                    "configuration" => [],
                                    "value" => null,
                                    "extraData" => [],
                                ]
                            ],
                            "attributes" => []
                        ]
                    ]
                ], [
                    "key" => "synopsis",
                    "type" => "textarea",
                    "values" => [
                        [
                            "uuid" => null,
                            "language" => "es",
                            "rules" => [
                                "required" => true
                            ],
                            "configuration" => [
                                "cols" => 10,
                                "rows" => 10
                            ],
                            "value" => null,
                            "extraData" => []
                        ], [
                            "uuid" => null,
                            "language" => "en",
                            "rules" => [
                                "required" => true
                            ],
                            "configuration" => [
                                "cols" => 10,
                                "rows" => 10
                            ],
                            "value" => null,
                            "extraData" => []
                        ]
                    ],
                    "attributes" => []
                ], [
                    "key" => "release-date",
                    "type" => "string",
                    "values" => [
                        [
                            "uuid" => null,
                            "language" => "es",
                            "rules" => [
                                "required" => true
                            ],
                            "configuration" => [
                                "cols" => 10,
                                "rows" => 10
                            ],
                            "value" => null,
                            "extraData" => []
                        ], [
                            "uuid" => null,
                            "language" => "en",
                            "rules" => [
                                "required" => false
                            ],
                            "configuration" => [
                                "cols" => 20,
                                "rows" => 20
                            ],
                            "value" => null,
                            "extraData" => []
                        ], [
                            "uuid" => null,
                            "language" => "+",
                            "rules" => [
                                "required" => true
                            ],
                            "configuration" => [
                                "cols" => 30,
                                "rows" => 30
                            ],
                            "value" => null,
                            "extraData" => []
                        ]
                    ],
                    "attributes" => []
                ], [
                    "key" => "code",
                    "type" => "lookup",
                    "values" => [
                        [
                            "uuid" => null,
                            "language" => "*",
                            "rules" => [
                                "required" => false
                            ],
                            "configuration" => [
                                "options" => [
                                    "pc-code",
                                    "playstation-code",
                                    "xbox-code",
                                    "switch-code",
                                ]
                            ],
                            "value" => null,
                            "extraData" => []
                        ]
                    ],
                    "attributes" => []
                ]
            ],
            "relations" => [],
        ], $instance->toArray());
    }
}
