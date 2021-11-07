<?php declare(strict_types=1);

namespace Tests\Editora\Data\Objects;

use Omatech\Mcore\Editora\Domain\Instance\Instance;

class MoviesMother extends ObjectMother
{
    public function get(int $instancesNumber = 1, ?string $key = null, ?array $relations = []): void
    {
    }

    public function emptyInstance(): Instance
    {
        return $this->build('Movies');
    }
}
