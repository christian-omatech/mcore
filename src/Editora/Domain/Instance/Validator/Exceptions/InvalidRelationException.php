<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions;

use Exception;

final class InvalidRelationException extends Exception
{
    public static function withRelation(string $key): self
    {
        throw new self("Relation ${key} is not valid.");
    }
}
