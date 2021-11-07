<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Tests\Editora\EditoraTestCase;

class InstanceExtractionQueryParserTest extends EditoraTestCase
{
    /** @test */
    public function queryParser(): void
    {
        $graphQuery = '{
            Books(language: en) {
                title,
                isbn,
                synopsis,
                picture {
                    alt
                }
                price
                Articles(limit: 1, page: 2, type: parent)
                Photos(limit: 3, type: "invalid-type") {
                    Location()
                }
            }
        }';
        $query = (new QueryParser())->parse($graphQuery)[0];
        $this->assertNull($query->param('non-existent-param'));
        $this->assertSame([
            'language' => 'en',
            'attributes' => [
                [
                    'key' => 'title',
                    'attributes' => [],
                ],  [
                    'key' => 'isbn',
                    'attributes' => [],
                ],  [
                    'key' => 'synopsis',
                    'attributes' => [],
                ], [
                    'key' => 'picture',
                    'attributes' => [
                        [
                            'key' => 'alt',
                            'attributes' => [],
                        ],
                    ],
                ], [
                    'key' => 'price',
                    'attributes' => [],
                ],
            ],
            'params' => [
                'language' => 'en',
                'class' => 'books',
                'key' => null,
                'preview' => false,
                'limit' => 0,
                'page' => 1,
            ],
            'relations' => [
                [
                    'language' => 'en',
                    'attributes' => [],
                    'params' => [
                        'limit' => 1,
                        'page' => 2,
                        'type' => 'parent',
                        'key' => 'articles',
                        'class' => null,
                        'preview' => false,
                        'language' => 'en',
                    ],
                    'relations' => [],
                    'pagination' => null,
                ], [
                    'language' => 'en',
                    'attributes' => [],
                    'params' => [
                        'limit' => 3,
                        'type' => 'child',
                        'key' => 'photos',
                        'class' => null,
                        'preview' => false,
                        'page' => 1,
                        'language' => 'en',
                    ],
                    'relations' => [
                        [
                            'language' => 'en',
                            'attributes' => [],
                            'params' => [
                                'limit' => 0,
                                'key' => 'location',
                                'class' => null,
                                'preview' => false,
                                'page' => 1,
                                'language' => 'en',
                                'type' => 'child',
                            ],
                            'relations' => [],
                            'pagination' => null,
                        ],
                    ],
                    'pagination' => null,
                ],
            ],
            'pagination' => null,
        ], $query->toArray());
    }
}
