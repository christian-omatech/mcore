<?php declare(strict_types=1);

namespace Tests\Editora\Data\Objects;

use function Lambdish\Phunctional\reduce;

class ArticlesMother extends ObjectMother
{
    protected array $availableRelations = [];

    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): array
    {
        $this->instances = [];
        for ($i = 1; $i <= $instancesNumber; $i++) {
            $this->instances[] = $this->build('Articles')->fill([
                'metadata' => [
                    'uuid' => $this->faker->uuid(),
                    'key' => $key ?? 'article-instance-'.$i,
                    'publication' => [
                        'startPublishingDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                    ],
                ],
                'attributes' => [
                    'title' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
                                'language' => $language,
                                'value' => $this->faker->sentence(),
                            ];
                            return $acc;
                        }, $this->languages, []),
                        'attributes' => [],
                    ],
                    'author' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
                                'language' => $language,
                                'value' => $this->faker->name(),
                            ];
                            return $acc;
                        }, $this->languages, []),
                        'attributes' => [],
                    ],
                    'page' => [
                        'values' => reduce(function (array $acc, string $language) {
                            $acc[] = [
                                'uuid' => $this->faker->uuid(),
                                'language' => $language,
                                'value' => null,
                            ];
                            return $acc;
                        }, $this->languages, []),
                    ],
                ],
                'relations' => $relations,
            ]);
        }
        return $this->instances;
    }
}
