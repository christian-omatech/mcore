<?php declare(strict_types=1);

namespace Tests\Editora\Data\Objects;

use function Lambdish\Phunctional\reduce;

class LocationsMother extends ObjectMother
{
    protected array $availableRelations = [];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        $this->instances = [];
        for ($i = 1; $i <= $instancesNumber; $i++) {
            $this->instances[] = $this->build('Locations')->fill([
                'metadata' => [
                    'uuid' => $this->faker->uuid(),
                    'key' => $key ?? 'location-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'country' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
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
