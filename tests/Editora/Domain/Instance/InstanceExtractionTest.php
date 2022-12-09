<?php

namespace Tests\Editora\Domain\Instance;

use Mockery;
use Omatech\MageCore\Editora\Domain\Extraction\Contracts\ExtractionInterface;
use Omatech\MageCore\Editora\Domain\Extraction\ExtractionBuilder;
use Omatech\MageCore\Editora\Domain\Extraction\Pagination;
use Omatech\MageCore\Editora\Domain\Extraction\Parser;
use Omatech\MageCore\Editora\Domain\Extraction\Results;
use Tests\Editora\Data\Objects\ArticleMother;
use Tests\Editora\Data\Objects\BooksMother;
use Tests\Editora\Data\Objects\LocationMother;
use Tests\Editora\Data\Objects\NewsMother;
use Tests\Editora\Data\Objects\PhotosMother;
use Tests\TestCase;

class InstanceExtractionTest extends TestCase
{
    /** @test */
    public function givenExtractionExpressionWhenOk()
    {
        $news = NewsMother::get(20);

        $graphQuery = '{
            News(preview: false, languages: [es], limit: 5, page: 1)
        }';

        $mock = $this->mockExtraction($graphQuery, [$news]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build();

    }

    /** @test */
    public function givenExtractionExpression2WhenOk()
    {
        $news = NewsMother::get(2);
        $articles = ArticleMother::get(2);

        $graphQuery = '{
            News(preview: false, languages: [es], limit: 5, page: 1)
            Articles(preview: false, languages: [es])
        }';

        $mock = $this->mockExtraction($graphQuery, [$news, $articles]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build();

    }

    /** @test */
    public function givenExtractionExpression3WhenOk()
    {
        $news = NewsMother::get(1);
        $articles = ArticleMother::get(1);

        $graphQuery = '{
            instances(key: "new-instance-1", languages: [es])
            instances(key: "article-instance-1", languages: [es], preview: true) {
                title
                author
            }
        }';

        $mock = $this->mockExtraction($graphQuery, [$news, $articles]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build();

    }

    /** @test */
    public function givenExtractionExpression4WhenOk()
    {
        $locations = LocationMother::get(1);
        $photos = PhotosMother::get(1, [ 'photos-locations' => $locations ]);
        $news = NewsMother::get(1, [ 'news-photos' => $photos ]);

        $graphQuery = '{
            News(languages: es) {
                title
                NewsPhotos(limit:7, page: 2) {
                    PhotosLocations(limit: 2) {
                        country
                    }
                }
            }
        }';

        $mock = $this->mockExtraction($graphQuery, [$news, $photos, $locations]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build();

    }

    /** @test */
    public function givenExtractionExpression5WhenOk()
    {

        $locations = LocationMother::get(1);
        $photos = PhotosMother::get(3, [ 'photos-locations' => $locations ]);
        $articles = ArticleMother::get(1);
        $books = BooksMother::get(1, [ 'articles' => $articles, 'photos' => $photos ]);

        $graphQuery = '{
            Books(languages: [en]) {
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
        }';

        $mock = $this->mockExtraction($graphQuery, [$books, $articles, $photos, $locations]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build();

    }

    private function mockExtraction(string $graphQuery, array $instances): ExtractionInterface
    {
        $mock = Mockery::mock(ExtractionInterface::class);
        $instance = array_shift($instances);
        $parsedQuery = (new Parser())->parse($graphQuery);
        foreach($parsedQuery as $index => $query) {
            $mock->shouldReceive('instancesBy')
                ->with($query->params())
                ->andReturn(new Results($instance, new Pagination([
                    'page' => $query->params()['page'],
                    'limit' => $query->params()['limit'],
                ], count($instance))))
                ->once();
            $this->mockRelations($mock, $query->relations(), $instances);
        }
        return $mock;
    }

    private function mockRelations(Mockery\MockInterface $mock, array $relations, $relatedInstances): void
    {
        foreach ($relations as $relation) {
            $instance = array_shift($relatedInstances);
            $mock->shouldReceive('findRelatedInstances')
                ->with('uuid', $relation->params())
                ->andReturn(new Results($instance, new Pagination([
                    'page' => $relation->params()['page'],
                    'limit' => $relation->params()['limit'],
                ], count($instance))))
                ->atLeast();
            if($relation->relations()) {
                $this->mockRelations($mock, $relation->relations(), $relatedInstances);
            }
        }
    }
}