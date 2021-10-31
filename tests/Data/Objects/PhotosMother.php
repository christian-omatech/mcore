<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use function Lambdish\Phunctional\reduce;

class PhotosMother extends ObjectMother
{
    protected array $availableRelations = [
        'photos-locations' => [ 
            LocationsMother::class,
            CoordinatesMother::class
        ],
    ];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        $this->instances = [];
        for ($i = 1; $i <= $instancesNumber; $i++) {
            $this->instances[] = $this->build('Photos')->fill([
                'metadata' => [
                    'uuid' => $this->faker->uuid(),
                    'key' => $key ?? 'photo-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'url' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
                                'language' => $language,
                                'value' => $this->faker->url(),
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
