<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules;

use Exception;

final class InvalidEndDatePublishingException extends Exception
{
    public static function withDate(string $endDate, string $startDate): self
    {
        throw new self("End publication date (${endDate}) is before 
        the initial publication date (${startDate}).");
    }
}
