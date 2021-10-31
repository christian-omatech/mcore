<?php declare(strict_types=1);

namespace Omatech\Mcore\Shared\Utils;

use function Lambdish\Phunctional\filter;

class Utils
{
    private static ?Utils $instance = null;

    public static function getInstance(): Utils
    {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function slug(?string $string): ?string
    {
        if ($this->isEmpty($string)) {
            return $string;
        }
        return strtolower(trim(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $string), '-'));
    }

    public function isEmpty(mixed $value): bool
    {
        return (bool) filter(static fn ($operator) => $value === $operator, ['', [], null]);
    }
}
