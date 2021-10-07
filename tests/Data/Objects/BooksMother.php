<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use function Lambdish\Phunctional\reduce;

class BooksMother extends ObjectMother
{
    protected array $availableRelations = [
        'articles' => [
            ArticlesMother::class,
        ],
        'photos' => [
            PhotosMother::class,
            PicturesMother::class,
        ],
    ];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        $this->instances = [];
        for ($i = 1; $i <= $instancesNumber; $i++) {
            $this->instances[] = $this->build('Books')->fill([
                'metadata' => [
                    'id' => $this->faker->randomNumber(),
                    'key' => $key ?? 'book-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'title' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'id' => $this->faker->randomNumber(),
                                'language' => $language,
                                'value' => $this->faker->sentence(),
                            ];
                            return $acc;
                        }, $this->languages, []),
                        'attributes' => [],
                    ],
                    'isbn' => [
                        'values' => array_merge(reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'id' => $this->faker->randomNumber(),
                                'language' => $language,
                                'value' => null,
                            ];
                            return $acc;
                        }, $this->languages, []), [[
                            'id' => $this->faker->randomNumber(),
                            'language' => '+',
                            'value' => $this->faker->isbn13(),
                        ],
                        ]),
                        'attributes' => [],
                    ],
                    'synopsis' => [
                        'values' => array_merge(reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'id' => $this->faker->randomNumber(),
                                'language' => $language,
                                'value' => $this->faker->paragraph(),
                            ];
                            return $acc;
                        }, $this->languages, []), [[
                            'id' => $this->faker->randomNumber(),
                            'language' => '+',
                            'value' => $this->faker->paragraph(),
                        ],
                        ]),
                        'attributes' => [],
                    ],
                    'picture' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'id' => $this->faker->randomNumber(),
                                'language' => $language,
                                'value' => $this->faker->url(),
                            ];
                            return $acc;
                        }, $this->languages, []),
                        'attributes' => [
                            'alt' => [
                                'values' => reduce(function (array $acc, string $language) {
                                    $acc[] = [
                                        'id' => $this->faker->randomNumber(),
                                        'language' => $language,
                                        'value' => $this->faker->sentence(),
                                    ];
                                    return $acc;
                                }, $this->languages, []),
                            ],
                        ],
                    ],
                    'price' => [
                        'values' => [
                            [
                                'id' => $this->faker->randomNumber(),
                                'language' => '*',
                                'value' => $this->faker->randomFloat(),
                            ],
                        ],
                    ],
                ],
                'relations' => $relations,
            ]);
        }
        return $this->instances;
    }
}
