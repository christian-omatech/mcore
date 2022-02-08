<?php declare(strict_types=1);

namespace Tests\Editora\Data\Objects;

use function Lambdish\Phunctional\reduce;

class NewsMother extends ObjectMother
{
    protected array $availableRelations = [
        'news-photos' => [ PhotosMother::class],
    ];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        $this->instances = [];
        for ($i = 1; $i <= $instancesNumber; $i++) {
            $this->instances[] = $this->build('News')->fill([
                'metadata' => [
                    'uuid' => $this->faker->uuid(),
                    'key' => $key ?? 'new-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'title' => [
                        'values' => array_merge(reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $language.'-'.$this->faker->uuid(),
                                'language' => $language,
                                'value' => null,
                            ];
                            return $acc;
                        }, $this->languages, []), [[
                            'uuid' => $this->faker->uuid(),
                            'language' => '+',
                            'value' => null,
                        ],
                        ]),
                        'attributes' => [],
                    ],
                    'description' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
                                'language' => $language,
                                'value' => $this->faker->paragraph(),
                            ];
                            return $acc;
                        }, $this->languages, []),
                        'attributes' => [],
                    ],
                ],
                'relations' => $relations,
            ]);
        }
        return $this->instances;
    }
}
