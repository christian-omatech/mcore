<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use function Lambdish\Phunctional\reduce;

class LocationsMother extends ObjectMother
{
    protected array $availableRelations = [];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        for ($i = 0; $i < $instancesNumber; $i++) {
            $this->instances[] = $this->build('Locations')->fill([
                'metadata' => [
                    'id' => $this->faker->randomNumber(),
                    'key' => $key ?? 'location-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'country' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'id' => $this->faker->randomNumber(),
                                'language' => $language,
                                'value' => $this->faker->country(),
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
