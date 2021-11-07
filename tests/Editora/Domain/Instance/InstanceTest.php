<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use DateTime;
use Omatech\Mcore\Editora\Domain\Clazz\Exceptions\InvalidRelationClassException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidEndDatePublishingException;
use Omatech\Mcore\Editora\Domain\Instance\PublicationStatus;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\InvalidRelationException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\InvalidRuleException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\RequiredValueException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueInDBException;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\LookupValueOptionException;
use Tests\Editora\Data\Objects\MoviesMother;
use Tests\Editora\Data\Objects\VideoGamesMother;
use Tests\Editora\EditoraTestCase;
use function Lambdish\Phunctional\search;

final class InstanceTest extends EditoraTestCase
{
    /** @test */
    public function instanceAttributeValidationFailed(): void
    {
        $this->expectException(RequiredValueException::class);

        $instance = (new VideoGamesMother())->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceLookupInvalidOption(): void
    {
        $this->expectException(LookupValueOptionException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'code' => [
                    'values' => [
                        [
                            'uuid' => '1',
                            'language' => '*',
                            'value' => 'hola',
                        ],
                    ],
                ],
            ]),
            'relations' => [],
        ]);
    }

    /** @test */
    public function invalidRuleWhenCreateInstance(): void
    {
        $this->expectException(InvalidRuleException::class);

        $instance = (new MoviesMother())->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceSubAttributeValidationFailed(): void
    {
        $this->expectException(RequiredValueException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'title' => [
                    'values' => [
                        [
                            'uuid' => null,
                            'language' => 'es',
                            'value' => 'titulo',
                        ], [
                            'uuid' => null,
                            'language' => 'en',
                            'value' => 'title',
                        ],
                    ],
                    'attributes' => [
                        'sub-title' => [
                            'values' => [
                                [
                                    'uuid' => null,
                                    'language' => 'es',
                                    'value' => null,
                                ], [
                                    'uuid' => null,
                                    'language' => 'en',
                                    'value' => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceInvalidRelation(): void
    {
        $this->expectException(InvalidRelationException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => $videoGamesMother->onlyFilledAttributes(),
            'relations' => [
                'non-valid-relation' => [
                    '1' => 'class-two',
                    '2' => 'class-two',
                    '3' => 'class-two',
                ],
            ],
        ]);
    }

    /** @test */
    public function instanceInvalidRelationClass(): void
    {
        $this->expectException(InvalidRelationClassException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => $videoGamesMother->onlyFilledAttributes(),
            'relations' => [
                'platforms' => [
                    '1' => 'non-valid-class',
                ],
            ],
        ]);
    }

    /** @test */
    public function instanceInvalidWhenEndDateIsLessThanStartDatePublishing(): void
    {
        $this->expectException(InvalidEndDatePublishingException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => [
                'key' => 'instance',
                'publication' => [
                    'status' => PublicationStatus::REVISION,
                    'startPublishingDate' => '2022-03-08 09:00:00',
                    'endPublishingDate' => '2021-07-27 14:30:00',
                ],
            ],
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceInvalidWhenEndDateIsEqualThanStartDatePublishing(): void
    {
        $this->expectException(InvalidEndDatePublishingException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => [
                'key' => 'instance',
                'publication' => [
                    'status' => PublicationStatus::REVISION,
                    'startPublishingDate' => '2022-03-08 09:00:00',
                    'endPublishingDate' => '2022-03-08 09:00:00',
                ],
            ],
            'attributes' => [],
            'relations' => [],
        ]);
    }

    /** @test */
    public function exceptionOnUniqueRuleInInstance(): void
    {
        $this->expectException(UniqueValueException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'title' => [
                    'values' => [
                        [
                            'uuid' => null,
                            'language' => 'es',
                            'value' => 'titulo',
                        ], [
                            'uuid' => null,
                            'language' => 'en',
                            'value' => 'title',
                        ],
                    ],
                    'attributes' => [
                        'code' => [
                            'values' => [
                                [
                                    'uuid' => null,
                                    'language' => 'es',
                                    'value' => 'playstation-code',
                                ], [
                                    'uuid' => null,
                                    'language' => 'en',
                                    'value' => 'playstation-code',
                                ],
                            ],
                        ],
                        'sub-title' => [
                            'values' => [
                                [
                                    'uuid' => null,
                                    'language' => 'es',
                                    'value' => 'sub-titulo',
                                ], [
                                    'uuid' => null,
                                    'language' => 'en',
                                    'value' => 'sub-title',
                                ],
                            ],
                        ],
                    ],
                ],
                'code' => [
                    'values' => [
                        [
                            'uuid' => null,
                            'language' => '*',
                            'value' => 'playstation-code',
                        ],
                    ],
                ],
            ]),
            'relations' => [],
        ]);
    }

    /** @test */
    public function exceptionOnUniqueRuleInDB(): void
    {
        $this->expectException(UniqueValueInDBException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'sub-title' => [
                    'values' => [
                        [
                            'uuid' => 'fake-uuid',
                            'language' => 'es',
                            'value' => 'sub-titulo',
                        ], [
                            'uuid' => 'fake-uuid-two',
                            'language' => 'en',
                            'value' => 'sub-title',
                        ],
                    ],
                ],
            ]),
            'relations' => [],
        ]);
    }

    /** @test */
    public function exceptionOnUniqueRuleInInstanceAndDB(): void
    {
        $this->expectException(UniqueValueException::class);

        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $this->fillMetadataInstance(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'sub-title' => [
                    'values' => [
                        [
                            'uuid' => 'fake-uuid',
                            'language' => 'es',
                            'value' => 'sub-sub-titulo',
                        ], [
                            'uuid' => 'fake-uuid-two',
                            'language' => 'en',
                            'value' => 'sub-sub-title',
                        ],
                    ],
                ],
            ]),
            'relations' => [],
        ]);
    }

    /** @test */
    public function instanceFilledWithNonValidAttribute(): void
    {
        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $videoGamesMother->filledMetadata(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'non-attribute' => [],
            ]),
            'relations' => [],
        ]);

        $results = search(static function (array $attribute) {
            return $attribute['key'] === 'non-attribute';
        }, $instance->toArray()['attributes']);

        $this->assertNull($results);
    }

    /** @test */
    public function instanceFilledWithNonValidAttributeLanguage(): void
    {
        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $videoGamesMother->filledMetadata(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'synopsis' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'hola mundo',
                        ], [
                            'language' => 'en',
                            'value' => 'hello world',
                        ], [
                            'language' => 'jp',
                            'value' => 'こんにちは世界',
                        ],
                    ],
                ],
            ]),
            'relations' => [],
        ]);

        $results = search(static function (array $attribute) {
            return $attribute['key'] === 'synopsis';
        }, $instance->toArray()['attributes']);

        $results = search(static function (array $value) {
            return $value['language'] === 'jp';
        }, $results['values']);

        $this->assertNull($results);
    }

    /** @test */
    public function instanceFilledCorrectly(): void
    {
        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $videoGamesMother->filledMetadata(),
            'attributes' => $videoGamesMother->onlyFilledAttributes(),
            'relations' => [
                'platforms' => [
                    'be0deb29-910f-4559-9aea-0b9b1d152e20' => 'platform',
                    '53ec408e-294a-4221-953f-dfc1aed08235' => 'platform',
                    '7aaa7fa5-75ba-461d-8d06-a5ae756f2e3e' => 'platform',
                    '0c187c3c-45ae-49f8-b9f4-85b4fc6b6f53' => 'platform',
                    '332f8de2-5789-4234-8497-85dbc2e67dc1' => 'platform',
                    'ef0b94ea-c042-43bd-9b12-cbc6c641be79' => 'platform',
                ],
                'reviews' => [
                    'ae72fe61-31eb-4811-bced-62418703791f' => 'articles',
                    '69dff245-252a-4483-8006-4d53c685c66f' => 'articles',
                    '7e271eb3-eba5-4ccb-b4d6-83fe00882848' => 'blogs',
                    '504d84c5-af31-48ed-9efc-dd825b3f6708' => 'blogs',
                    'c04694b3-8d59-4492-92a5-9730277aef9a' => 'blogs',
                ],
            ],
        ]);
        $this->assertEquals([
            'classKey' => 'video-games',
            'key' => 'video-game-instance',
            'status' => 'in-revision',
            'startPublishingDate' => DateTime::createFromFormat('Y-m-d H:i:s', '1989-03-08 09:00:00'),
            'endPublishingDate' => DateTime::createFromFormat('Y-m-d H:i:s', '2021-07-27 14:30:00'),
        ], $instance->data());
        $this->assertEquals('custom-uuid', $instance->uuid());
        $this->assertIsArray($instance->attributes()->toArray());
        $this->assertEquals([
            [
                'key' => 'platforms',
                'instances' => [
                    'be0deb29-910f-4559-9aea-0b9b1d152e20' => 'platform',
                    '53ec408e-294a-4221-953f-dfc1aed08235' => 'platform',
                    '7aaa7fa5-75ba-461d-8d06-a5ae756f2e3e' => 'platform',
                    '0c187c3c-45ae-49f8-b9f4-85b4fc6b6f53' => 'platform',
                    '332f8de2-5789-4234-8497-85dbc2e67dc1' => 'platform',
                    'ef0b94ea-c042-43bd-9b12-cbc6c641be79' => 'platform',
                ],
            ], [
                'key' => 'reviews',
                'instances' => [
                    'ae72fe61-31eb-4811-bced-62418703791f' => 'articles',
                    '69dff245-252a-4483-8006-4d53c685c66f' => 'articles',
                    '7e271eb3-eba5-4ccb-b4d6-83fe00882848' => 'blogs',
                    '504d84c5-af31-48ed-9efc-dd825b3f6708' => 'blogs',
                    'c04694b3-8d59-4492-92a5-9730277aef9a' => 'blogs',
                ],
            ],
        ], $instance->relations()->toArray());

        $this->assertEquals([
            'class' => [
                'key' => 'video-games',
                'relations' => [
                    [
                        'key' => 'platforms',
                        'classes' => [
                            'platform',
                        ],
                    ], [
                        'key' => 'reviews',
                        'classes' => [
                            'articles',
                            'blogs',
                        ],
                    ],
                ],
            ],
            'metadata' => [
                'uuid' => 'custom-uuid',
                'key' => 'video-game-instance',
                'publication' => [
                    'status' => 'in-revision',
                    'startPublishingDate' => '1989-03-08 09:00:00',
                    'endPublishingDate' => '2021-07-27 14:30:00',
                ],
            ],
            'attributes' => [
                [
                    'key' => 'title',
                    'type' => 'string',
                    'values' => [
                        [
                            'uuid' => 'c342193b-1c16-3077-af26-84cf15acc9a2',
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'titulo',
                            'extraData' => [],
                        ], [
                            'uuid' => 'f3078d87-e366-3506-9077-d24a872af11c',
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'title',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [
                        [
                            'key' => 'code',
                            'type' => 'string',
                            'values' => [
                                [
                                    'uuid' => '40f91d9f-51c2-36a2-815c-922e20136bee',
                                    'language' => 'es',
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => null,
                                    'extraData' => [],
                                ], [
                                    'uuid' => '818e45fc-6d6c-3352-a69b-37c1ebf720a2',
                                    'language' => 'en',
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => null,
                                    'extraData' => [],
                                ],
                            ],
                            'attributes' => [],
                        ], [
                            'key' => 'sub-title',
                            'type' => 'string',
                            'values' => [
                                [
                                    'uuid' => '65b12889-81d8-3068-a1ca-202dcd3ee4a6',
                                    'language' => 'es',
                                    'rules' => [
                                        'required' => true,
                                    ],
                                    'configuration' => [],
                                    'value' => 'sub-sub-titulo',
                                    'extraData' => [],
                                ], [
                                    'uuid' => '84968337-90d0-3412-b33f-5afd23e39c9f',
                                    'language' => 'en',
                                    'rules' => [
                                        'required' => true,
                                    ],
                                    'configuration' => [],
                                    'value' => 'sub-sub-title',
                                    'extraData' => [],
                                ],
                            ],
                            'attributes' => [],
                        ],
                    ],
                ], [
                    'key' => 'sub-title',
                    'type' => 'string',
                    'values' => [
                        [
                            'uuid' => '8b105764-6fb3-3f94-a2a2-5360064cab40',
                            'language' => 'es',
                            'rules' => [
                                'uniqueDB' => [
                                    'class' => "Tests\Editora\Data\UniqueValueRepository",
                                ],
                            ],
                            'configuration' => [],
                            'value' => 'sub-titulo',
                            'extraData' => [],
                        ], [
                            'uuid' => '592f4da6-2243-34e0-af33-6c2b1a5ebabc',
                            'language' => 'en',
                            'rules' => [
                                'uniqueDB' => [
                                    'class' => "Tests\Editora\Data\UniqueValueRepository",
                                ],
                            ],
                            'configuration' => [],
                            'value' => 'sub-title',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ], [
                    'key' => 'synopsis',
                    'type' => 'textarea',
                    'values' => [
                        [
                            'uuid' => 'bc011d62-60a1-3d3c-8983-9126bfa4261b',
                            'language' => 'es',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 10,
                                'rows' => 10,
                            ],
                            'value' => 'sinopsis',
                            'extraData' => [],
                        ], [
                            'uuid' => 'ec28da68-3b91-3aeb-9d44-f08778576426',
                            'language' => 'en',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 10,
                                'rows' => 10,
                            ],
                            'value' => 'synopsis',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ], [
                    'key' => 'release-date',
                    'type' => 'string',
                    'values' => [
                        [
                            'uuid' => 'fe49b765-9b45-3b68-9467-c4b4a5154946',
                            'language' => 'es',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 10,
                                'rows' => 10,
                            ],
                            'value' => 'fecha-salida',
                            'extraData' => [],
                        ], [
                            'uuid' => '798f58a9-df29-36f9-b4c8-3028c6ddcf2f',
                            'language' => 'en',
                            'rules' => [
                                'required' => false,
                            ],
                            'configuration' => [
                                'cols' => 20,
                                'rows' => 20,
                            ],
                            'value' => 'release-date',
                            'extraData' => [],
                        ], [
                            'uuid' => 'de17be73-260a-38ed-bef4-f7fdfae85201',
                            'language' => '+',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 30,
                                'rows' => 30,
                            ],
                            'value' => 'default-date',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ], [
                    'key' => 'code',
                    'type' => 'lookup',
                    'values' => [
                        [
                            'uuid' => 'f6e5c12e-99f4-3d5e-b8a8-0fc702b9fda7',
                            'language' => '*',
                            'rules' => [
                                'required' => true,
                                'unique' => [],
                            ],
                            'configuration' => [
                                'options' => [
                                    'pc-code', 'playstation-code', 'xbox-code', 'switch-code',
                                ],
                            ],
                            'value' => 'playstation-code',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ],
            ],
            'relations' => [
                [
                    'key' => 'platforms',
                    'instances' => [
                        'be0deb29-910f-4559-9aea-0b9b1d152e20' => 'platform',
                        '53ec408e-294a-4221-953f-dfc1aed08235' => 'platform',
                        '7aaa7fa5-75ba-461d-8d06-a5ae756f2e3e' => 'platform',
                        '0c187c3c-45ae-49f8-b9f4-85b4fc6b6f53' => 'platform',
                        '332f8de2-5789-4234-8497-85dbc2e67dc1' => 'platform',
                        'ef0b94ea-c042-43bd-9b12-cbc6c641be79' => 'platform',
                    ],
                ], [
                    'key' => 'reviews',
                    'instances' => [
                        'ae72fe61-31eb-4811-bced-62418703791f' => 'articles',
                        '69dff245-252a-4483-8006-4d53c685c66f' => 'articles',
                        '7e271eb3-eba5-4ccb-b4d6-83fe00882848' => 'blogs',
                        '504d84c5-af31-48ed-9efc-dd825b3f6708' => 'blogs',
                        'c04694b3-8d59-4492-92a5-9730277aef9a' => 'blogs',
                    ],
                ],
            ],
        ], $instance->toArray());
    }

    /** @test */
    public function instanceUpdateSuccessfully(): void
    {
        $videoGamesMother = new VideoGamesMother();
        $instance = $videoGamesMother->emptyInstance();

        $instance->fill([
            'metadata' => $videoGamesMother->filledMetadata(),
            'attributes' => $videoGamesMother->onlyFilledAttributes(),
            'relations' => [],
        ]);

        $instance->fill([
            'metadata' => $videoGamesMother->filledMetadata(),
            'attributes' => $videoGamesMother->onlyFilledAttributes([
                'synopsis' => [
                    'values' => [
                        [
                            'uuid' => 'bc011d62-60a1-3d3c-8983-9126bfa4261b',
                            'language' => 'es',
                            'value' => 'sinopsis-editada',
                        ], [
                            'uuid' => 'ec28da68-3b91-3aeb-9d44-f08778576426',
                            'language' => 'en',
                            'value' => 'synopsis-edited',
                        ],
                    ],
                ],
            ]),
            'relations' => [],
        ]);

        $this->assertEquals([
            'class' => [
                'key' => 'video-games',
                'relations' => [
                    [
                        'key' => 'platforms',
                        'classes' => [
                            'platform',
                        ],
                    ], [
                        'key' => 'reviews',
                        'classes' => [
                            'articles',
                            'blogs',
                        ],
                    ],
                ],
            ],
            'metadata' => [
                'uuid' => 'custom-uuid',
                'key' => 'video-game-instance',
                'publication' => [
                    'status' => 'in-revision',
                    'startPublishingDate' => '1989-03-08 09:00:00',
                    'endPublishingDate' => '2021-07-27 14:30:00',
                ],
            ],
            'attributes' => [
                [
                    'key' => 'title',
                    'type' => 'string',
                    'values' => [
                        [
                            'uuid' => 'c342193b-1c16-3077-af26-84cf15acc9a2',
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'titulo',
                            'extraData' => [],
                        ], [
                            'uuid' => 'f3078d87-e366-3506-9077-d24a872af11c',
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'title',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [
                        [
                            'key' => 'code',
                            'type' => 'string',
                            'values' => [
                                [
                                    'uuid' => '40f91d9f-51c2-36a2-815c-922e20136bee',
                                    'language' => 'es',
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => null,
                                    'extraData' => [],
                                ], [
                                    'uuid' => '818e45fc-6d6c-3352-a69b-37c1ebf720a2',
                                    'language' => 'en',
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => null,
                                    'extraData' => [],
                                ],
                            ],
                            'attributes' => [],
                        ], [
                            'key' => 'sub-title',
                            'type' => 'string',
                            'values' => [
                                [
                                    'uuid' => '65b12889-81d8-3068-a1ca-202dcd3ee4a6',
                                    'language' => 'es',
                                    'rules' => [
                                        'required' => true,
                                    ],
                                    'configuration' => [],
                                    'value' => 'sub-sub-titulo',
                                    'extraData' => [],
                                ], [
                                    'uuid' => '84968337-90d0-3412-b33f-5afd23e39c9f',
                                    'language' => 'en',
                                    'rules' => [
                                        'required' => true,
                                    ],
                                    'configuration' => [],
                                    'value' => 'sub-sub-title',
                                    'extraData' => [],
                                ],
                            ],
                            'attributes' => [],
                        ],
                    ],
                ], [
                    'key' => 'sub-title',
                    'type' => 'string',
                    'values' => [
                        [
                            'uuid' => '8b105764-6fb3-3f94-a2a2-5360064cab40',
                            'language' => 'es',
                            'rules' => [
                                'uniqueDB' => [
                                    'class' => "Tests\Editora\Data\UniqueValueRepository",
                                ],
                            ],
                            'configuration' => [],
                            'value' => 'sub-titulo',
                            'extraData' => [],
                        ], [
                            'uuid' => '592f4da6-2243-34e0-af33-6c2b1a5ebabc',
                            'language' => 'en',
                            'rules' => [
                                'uniqueDB' => [
                                    'class' => "Tests\Editora\Data\UniqueValueRepository",
                                ],
                            ],
                            'configuration' => [],
                            'value' => 'sub-title',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ], [
                    'key' => 'synopsis',
                    'type' => 'textarea',
                    'values' => [
                        [
                            'uuid' => 'bc011d62-60a1-3d3c-8983-9126bfa4261b',
                            'language' => 'es',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 10,
                                'rows' => 10,
                            ],
                            'value' => 'sinopsis-editada',
                            'extraData' => [],
                        ], [
                            'uuid' => 'ec28da68-3b91-3aeb-9d44-f08778576426',
                            'language' => 'en',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 10,
                                'rows' => 10,
                            ],
                            'value' => 'synopsis-edited',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ], [
                    'key' => 'release-date',
                    'type' => 'string',
                    'values' => [
                        [
                            'uuid' => 'fe49b765-9b45-3b68-9467-c4b4a5154946',
                            'language' => 'es',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 10,
                                'rows' => 10,
                            ],
                            'value' => 'fecha-salida',
                            'extraData' => [],
                        ], [
                            'uuid' => '798f58a9-df29-36f9-b4c8-3028c6ddcf2f',
                            'language' => 'en',
                            'rules' => [
                                'required' => false,
                            ],
                            'configuration' => [
                                'cols' => 20,
                                'rows' => 20,
                            ],
                            'value' => 'release-date',
                            'extraData' => [],
                        ], [
                            'uuid' => 'de17be73-260a-38ed-bef4-f7fdfae85201',
                            'language' => '+',
                            'rules' => [
                                'required' => true,
                            ],
                            'configuration' => [
                                'cols' => 30,
                                'rows' => 30,
                            ],
                            'value' => 'default-date',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ], [
                    'key' => 'code',
                    'type' => 'lookup',
                    'values' => [
                        [
                            'uuid' => 'f6e5c12e-99f4-3d5e-b8a8-0fc702b9fda7',
                            'language' => '*',
                            'rules' => [
                                'required' => true,
                                'unique' => [],
                            ],
                            'configuration' => [
                                'options' => [
                                    'pc-code', 'playstation-code', 'xbox-code', 'switch-code',
                                ],
                            ],
                            'value' => 'playstation-code',
                            'extraData' => [],
                        ],
                    ],
                    'attributes' => [],
                ],
            ],
            'relations' => [],
        ], $instance->toArray());
    }

    private function fillMetadataInstance()
    {
        return [
            'key' => 'instance',
            'publication' => [
                'startPublishingDate' => '1989-03-08 09:00:00',
            ],
        ];
    }
}
