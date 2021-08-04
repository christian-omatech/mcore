<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Instance\Exceptions;

use Exception;

class InvalidRelationException extends Exception
{
    public static function withRelations(array $relations): InvalidRelationException
    {
        $relations = implode(', ', $relations);
        throw new self("Relations ${relations} not allowed.");
    }
}
