<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Attribute\Contracts;

interface AttributeRepositoryInterface
{
    public function classKeyWithAlternateNiceUrls(string $niceUrl): array;
}
