<?php declare(strict_types=1);

namespace Tests\Editora\Application;

use Mockery;
use Omatech\Mcore\Editora\Application\ExtractInstance\ExtractInstanceCommand;
use Omatech\Mcore\Editora\Application\ExtractInstance\ExtractInstanceCommandHandler;
use Omatech\Mcore\Editora\Domain\Extraction\Pagination;
use Omatech\Mcore\Editora\Domain\Extraction\Results;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Tests\Editora\Data\Objects\ArticlesMother;
use Tests\Editora\Data\Objects\BooksMother;
use Tests\Editora\Data\Objects\NewsMother;
use Tests\Editora\Data\Objects\ObjectMother;
use Tests\Editora\EditoraTestCase;

class ExtractInstanceTest extends EditoraTestCase
{
    /** @test */
    public function extractInstancesByClassSuccessfully(): void
    {
        $news = new NewsMother();
        $instances = $news->create(20);

        $repository = Mockery::mock(ExtractionRepositoryInterface::class);
        $repository->shouldReceive('instancesBy')
            ->with([
                'key' => null,
                'class' => 'news',
                'preview' => false,
                'languages' => ['es', 'en'],
                'limit' => 5,
                'page' => 1,
            ])
            ->andReturn(
                new Results(
                    $instances['instances'],
                    new Pagination([
                        'page' => 1,
                        'limit' => 5,
                    ], count($instances['instances']))
                )
            )->once();

        $command = new ExtractInstanceCommand('{
            News(preview: false, languages: [es, en], limit: 5, page: 1)
        }');
        $extractions = (new ExtractInstanceCommandHandler($repository, $this->mockExtractionCache()))->__invoke($command);
        $this->assertEquals(ObjectMother::extraction($instances['instances'], [
            'title',
            'description',
        ], 'es'), $extractions->toArray());
        $this->assertSame([
            'total' => 20,
            'limit' => 5,
            'current' => 1,
            'pages' => 4,
        ], $extractions->queries()[0]->pagination()->toArray());
    }

    /** @test */
    public function extractInstancesByMultipleClassesSuccessfully(): void
    {
        $news = new NewsMother();
        $newsInstances = $news->create(2);

        $repository = Mockery::mock(ExtractionRepositoryInterface::class);
        $repository->shouldReceive('instancesBy')
            ->with([
                'key' => null,
                'class' => 'news',
                'preview' => false,
                'language' => 'es',
                'limit' => 0,
                'page' => 1,
            ])
            ->andReturn(
                new Results(
                    $newsInstances['instances'],
                    new Pagination([
                        'page' => 1,
                        'limit' => 0,
                    ], count($newsInstances['instances']))
                )
            )->once();

        $articles = new ArticlesMother();
        $articlesInstances = $articles->create(1);

        $repository->shouldReceive('instancesBy')
            ->with([
                'key' => null,
                'class' => 'articles',
                'preview' => false,
                'language' => 'en',
                'limit' => 0,
                'page' => 1,
            ])
            ->andReturn(
                new Results(
                    $articlesInstances['instances'],
                    new Pagination([
                        'page' => 1,
                        'limit' => 0,
                    ], count($articlesInstances['instances']))
                )
            )->once();

        $command = new ExtractInstanceCommand('{
            News(preview: false, language: es)
            Articles(preview: false, language: en) {
                title
                author
            }
        }');
        $extractions = (new ExtractInstanceCommandHandler($repository, $this->mockExtractionCache()))->__invoke($command);

        $this->assertEquals([
            ObjectMother::extraction($newsInstances['instances'], ['title', 'description'], 'es'),
            ObjectMother::extraction($articlesInstances['instances'], ['title', 'author'], 'en'),
        ], $extractions->toArray());
        $this->assertSame([
            'total' => 2,
            'limit' => 0,
            'current' => 1,
            'pages' => 1,
        ], $extractions->queries()[0]->pagination()->toArray());
        $this->assertSame([
            'total' => 1,
            'limit' => 0,
            'current' => 1,
            'pages' => 1,
        ], $extractions->queries()[1]->pagination()->toArray());
    }

    /** @test */
    public function extractInstancesByKeySuccessfully(): void
    {
        $news = new NewsMother();
        $newsInstance = $news->asKey('i-am-a-new');

        $repository = Mockery::mock(ExtractionRepositoryInterface::class);
        $repository->shouldReceive('instancesBy')
            ->with([
                'class' => null,
                'key' => 'i-am-a-new',
                'preview' => false,
                'language' => 'es',
                'limit' => 0,
                'page' => 1,
            ])
            ->andReturn(
                new Results(
                    $newsInstance,
                    new Pagination([
                        'page' => 1,
                        'limit' => 0,
                    ], count($newsInstance))
                )
            )->once();

        $articles = new ArticlesMother();
        $articleInstance = $articles->asKey('i-am-an-article');

        $repository->shouldReceive('instancesBy')
            ->with([
                'class' => null,
                'key' => 'i-am-an-article',
                'preview' => true,
                'language' => 'es',
                'limit' => 0,
                'page' => 1,
            ])
            ->andReturn(
                new Results(
                    $articleInstance,
                    new Pagination([
                        'page' => 1,
                        'limit' => 0,
                    ], count($articleInstance))
                )
            )->once();

        $graphQuery = '{
            instances(key: "i-am-a-new", language: es)
            instances(key: "i-am-an-article", language: es, preview: true) {
                title
                author
            }
        }';
        $command = new ExtractInstanceCommand($graphQuery);
        $extractions = (new ExtractInstanceCommandHandler($repository, $this->mockExtractionCache()))->__invoke($command);

        $this->assertEquals($graphQuery, $extractions->query());
        $this->assertEquals(md5($graphQuery), $extractions->hash());
        $this->assertInstanceOf('dateTime', $extractions->date());
        $this->assertIsArray($extractions->queries());

        $this->assertEquals([
            ObjectMother::extraction($newsInstance, ['title', 'description'], 'es'),
            ObjectMother::extraction($articleInstance, ['title', 'author'], 'es'),
        ], $extractions->toArray());
        $this->assertSame([
            'total' => 1,
            'limit' => 0,
            'current' => 1,
            'pages' => 1,
        ], $extractions->queries()[0]->pagination()->toArray());
        $this->assertFalse($extractions->queries()[0]->param('preview'));
        $this->assertEquals(1, $extractions->queries()[0]->pagination()->realLimit());
        $this->assertEquals(0, $extractions->queries()[0]->pagination()->offset());
        $this->assertSame([
            'total' => 1,
            'limit' => 0,
            'current' => 1,
            'pages' => 1,
        ], $extractions->queries()[1]->pagination()->toArray());
        $this->assertEquals(1, $extractions->queries()[1]->pagination()->realLimit());
        $this->assertEquals(0, $extractions->queries()[1]->pagination()->offset());
        $this->assertTrue($extractions->queries()[1]->param('preview'));
    }

    /** @test */
    public function extractInstancesWithRelationsSuccessfully(): void
    {
        $news = new NewsMother();
        $newsInstances = $news->create(1, null, [
            'news-photos' => [
                'instances' => 10,
                'relations' => [
                    'photos-locations' => [
                        'instances' => 1,
                    ],
                ],
            ],
        ]);

        $repository = Mockery::mock(ExtractionRepositoryInterface::class);
        $repository->shouldReceive('instancesBy')
            ->with([
                'class' => 'news',
                'key' => null,
                'preview' => false,
                'language' => 'es',
                'limit' => '0',
                'page' => '1',
            ])
            ->andReturn(
                new Results(
                    $newsInstances['instances'],
                    new Pagination([
                        'page' => 1,
                        'limit' => 0,
                    ], count($newsInstances['instances']))
                )
            )->once();

        foreach ($newsInstances['instances'] as $instance) {
            $repository->shouldReceive('findRelatedInstances')
                ->with($instance->uuid(), [
                    'limit' => 7,
                    'class' => null,
                    'key' => 'news-photos',
                    'preview' => false,
                    'page' => '2',
                    'language' => 'es',
                    'type' => 'child',
                ])
                ->andReturn(
                    new Results(
                        $newsInstances['relations']['news-photos']['instances'],
                        new Pagination([
                            'page' => 2,
                            'limit' => 7,
                        ], count($newsInstances['relations']['news-photos']['instances']))
                    )
                )->once();
        }

        foreach ($newsInstances['relations']['news-photos']['instances'] as $instance) {
            $repository->shouldReceive('findRelatedInstances')
                ->with($instance->uuid(), [
                    'class' => null,
                    'key' => 'photos-locations',
                    'limit' => 2,
                    'language' => 'es',
                    'preview' => false,
                    'page' => 1,
                    'type' => 'child',
                ])
                ->andReturn(
                    new Results(
                        $newsInstances['relations']['news-photos']['relations']['photos-locations']['instances'],
                        new Pagination([
                            'page' => 1,
                            'limit' => 2,
                        ], count($newsInstances['relations']['news-photos']['relations']['photos-locations']['instances']))
                    )
                )->times(1);
        }

        $command = new ExtractInstanceCommand('{
            News(language: es) {
                title
                NewsPhotos(limit:7, page: 2) {
                    PhotosLocations(limit: 2) {
                        country
                    }
                }
            }
        }');
        $extraction = (new ExtractInstanceCommandHandler($repository, $this->mockExtractionCache()))->__invoke($command);

        $this->assertEquals(
            ObjectMother::extraction($newsInstances['instances'], ['title'], 'es', [
                'news-photos' => ['child' => ObjectMother::extraction($newsInstances['relations']['news-photos']['instances'], ['url'], 'es', [
                    'photos-locations' => ['child' => ObjectMother::extraction($newsInstances['relations']['news-photos']['relations']['photos-locations']['instances'], ['country'], 'es')],
                ]),
                ],
            ]),
            $extraction->toArray()
        );
        $this->assertSame([
            'total' => 10,
            'limit' => 7,
            'current' => 2,
            'pages' => 2,
        ], $extraction->queries()[0]->relations()[0]->pagination()->toArray());
    }

    /** @test */
    public function complexInstanceQueryExtraction(): void
    {
        $books = new BooksMother();
        $booksInstances = $books->create(1, null, [
            'articles' => [
                'instances' => 1,
            ],
            'photos' => [
                'instances' => 3,
                'relations' => [
                    'photos-locations' => [
                        'instances' => 1,
                    ],
                ],
            ],
        ]);
        $repository = Mockery::mock(ExtractionRepositoryInterface::class);
        $repository->shouldReceive('instancesBy')
            ->with([
                'class' => 'books',
                'key' => null,
                'preview' => false,
                'language' => 'en',
                'limit' => '0',
                'page' => '1',
            ])
            ->andReturn(
                new Results(
                    $booksInstances['instances'],
                    new Pagination([
                        'page' => 1,
                        'limit' => 0,
                    ], count($booksInstances['instances']))
                )
            )->once();

        foreach ($booksInstances['instances'] as $bookInstance) {
            $repository->shouldReceive('findRelatedInstances')
                ->with($bookInstance->uuid(), [
                    'limit' => 1,
                    'class' => null,
                    'key' => 'articles',
                    'preview' => false,
                    'page' => 1,
                    'language' => 'en',
                    'type' => 'child',
                ])->andReturn(
                    new Results(
                        $booksInstances['relations']['articles']['instances'],
                        new Pagination([
                            'page' => 1,
                            'limit' => 0,
                        ], count($booksInstances['relations']['articles']['instances']))
                    )
                )->once();
            $repository->shouldReceive('findRelatedInstances')
                ->with($bookInstance->uuid(), [
                    'limit' => 3,
                    'class' => null,
                    'key' => 'photos',
                    'preview' => false,
                    'page' => 1,
                    'language' => 'en',
                    'type' => 'child',
                ])->andReturn(
                    new Results(
                        $booksInstances['relations']['photos']['instances'],
                        new Pagination([
                            'page' => 1,
                            'limit' => 3,
                        ], count($booksInstances['relations']['photos']['instances']))
                    )
                )->once();

            $photosInstances = $booksInstances['relations']['photos']['instances'];
            foreach ($photosInstances as $photosInstance) {
                $repository->shouldReceive('findRelatedInstances')
                    ->with($photosInstance->uuid(), [
                        'limit' => 1,
                        'class' => null,
                        'key' => 'photos-locations',
                        'preview' => false,
                        'page' => 1,
                        'language' => 'en',
                        'type' => 'child',
                    ])->andReturn(
                        new Results(
                            $booksInstances['relations']['photos']['relations']['photos-locations']['instances'],
                            new Pagination([
                                'page' => 1,
                                'limit' => 1,
                            ], count($booksInstances['relations']['photos']['relations']['photos-locations']['instances']))
                        )
                    );
            }
        }

        $command = new ExtractInstanceCommand('{
            Books(language: en) {
                title,
                isbn,
                synopsis,
                picture {
                    alt
                }
                price
                Articles(limit: 1)
                Photos(limit: 3) {
                    PhotosLocations(limit: 1)
                }
            }
        }');

        $extraction = (new ExtractInstanceCommandHandler($repository, $this->mockExtractionCache()))->__invoke($command);

        $this->assertEquals(
            ObjectMother::extraction($booksInstances['instances'], [
                'title',
                'isbn',
                'synopsis',
                'picture' => [
                    'alt',
                ],
                'price',
            ], 'en', [
                'articles' => ['child' => [ObjectMother::extraction($booksInstances['relations']['articles']['instances'], [
                    'title',
                    'author',
                    'page',
                ], 'en', []),
                ],
                ],
                'photos' => ['child' => ObjectMother::extraction($booksInstances['relations']['photos']['instances'], [
                    'url',
                ], 'en', [
                    'photos-locations' => ['child' => ObjectMother::extraction(
                        $booksInstances['relations']['photos']['relations']['photos-locations']['instances'],
                        [
                            'country',
                            'latitude',
                            'longitude',
                        ],
                        'en'
                    ),
                    ],
                ]),
                ],
            ]),
            $extraction->toArray()
        );
    }
}
