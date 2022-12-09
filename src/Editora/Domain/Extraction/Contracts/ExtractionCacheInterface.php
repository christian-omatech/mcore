<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction\Contracts;

use Omatech\MageCore\Editora\Domain\Extraction\Extraction;

interface ExtractionCacheInterface
{
    public function get(string $hash): ?Extraction;
    public function put(string $hash, Extraction $extraction): void;
}
