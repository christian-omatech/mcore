<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use function Lambdish\Phunctional\reduce;

class CoordinatesMother extends ObjectMother
{
    protected array $availableRelations = [];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        $this->instances = [];
        for ($i = 1; $i <= $instancesNumber; $i++) {
            $this->instances[] = $this->build('Coordinates')->fill([
                'metadata' => [
                    'uuid' => $this->faker->uuid(),
                    'key' => $key ?? 'coordinate-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'latitude' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
                                'language' => $language,
                                'value' => $this->faker->randomDigit(),
                            ];
                            return $acc;
                        }, $this->languages, []),
                        'attributes' => [],
                    ],
                    'longitude' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
                                'language' => $language,
                                'value' => $this->faker->randomDigit(),
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
