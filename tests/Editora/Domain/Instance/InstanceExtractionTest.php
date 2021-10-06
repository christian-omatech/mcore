<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extractor;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\QueryParser;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Results;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;

class InstanceExtractionTest extends TestCase
{
    /** @test */
    public function extractInstanceWithQueryExtraction(): void
    {
        $query = '{
            instances(key: InstanceKey, language: es, limit: 0, page: 1) {
                DefaultAttribute
                AnotherAttribute
                AnotherOtherAttribute
                MultiAttribute
            }
        }';
        $query = (new QueryParser())->parse($query)[0];

        $instance = $this->instance('instance-key', [
            'attributes' => [
                'DefaultAttribute' => [
                    'attributes' => [
                        'SubDefaultAttribute' => [],
                    ],
                ],
            ],
        ], [
            'default-attribute' => [
                'values' => [
                    [
                        'id' => 1,
                        'language' => 'es',
                        'value' => 'hola',
                    ], [
                        'language' => 'en',
                        'value' => 'hello',
                    ],
                ],
            ],
        ], []);




        $extractor = new Extractor($query, $instance, []);
        $extraction = $extractor->extract();

        $this->assertSame([
            'key' => 'instance-key',
            'attributes' => [
                [
                    'id' => 1,
                    'key' => 'default-attribute',
                    'value' => 'hola',
                    'attributes' => [
                        [
                            'id' => null,
                            'key' => 'sub-default-attribute',
                            'value' => null,
                            'attributes' => [],
                        ],
                    ],
                ],
            ],
            'relations' => [],
        ], $extraction->toArray());
    }

    private function instance(string $key, array $structure, array $attributes, array $relations): Instance
    {
        $instance = (new InstanceBuilder($this->mockInstanceCache()))
            ->setLanguages(['es', 'en'])
            ->setStructure($structure)
            ->setClassName('ClassOne')
            ->build();

        return $instance->fill([
            'metadata' => [
                'key' => $key,
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => $attributes,
            'relations' => $relations,
        ]);
    }
}
