<?php declare(strict_types=1);

namespace Tests\Data\Objects;

use function Lambdish\Phunctional\reduce;

class PicturesMother extends ObjectMother
{
    protected array $availableRelations = [];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        for ($i = 0; $i < $instancesNumber; $i++) {
            $this->instances[] = $this->build('Pictures')->fill([
                'metadata' => [
                    'id' => $this->faker->randomNumber(),
                    'key' => $key ?? 'photo-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'url' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'id' => $this->faker->randomNumber(),
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
