<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use function Lambdish\Phunctional\reduce;

class NewsMother extends ObjectMother
{
    protected array $availableRelations = [
        'news-photos' => [ PhotosMother::class, ]
    ];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        for ($i = 0; $i < $instancesNumber; $i++) {
            $this->instances[] = $this->build('News')->fill([
                'metadata' => [
                    'id' => $this->faker->randomNumber(),
                    'key' => $key ?? 'new-instance-'.$i,
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
                    'description' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'id' => $this->faker->randomNumber(),
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
