<?php declare(strict_types=1);

namespace Omatech\Mcore\Shared\Utils;

use Cocur\Slugify\Slugify;
use function Lambdish\Phunctional\filter;

class Utils
{
    private static ?Utils $instance = null;
    private Slugify $slugify;

    private function __construct()
    {
        $this->slugify = new Slugify();
    }

    public static function getInstance(): Utils
    {
        if (! self::$instance) {
            self::$instance = new Utils();
        }
        return self::$instance;
    }

    public function slug(?string $string): ?string
    {
        if ($this->isEmpty($string)) {
            return $string;
        }
        return $this->slugify->slugify($string, [
            'regexp' => '/(?<=[[:^upper:]])(?=[[:upper:]])/',
            'lowercase_after_regexp' => true,
        ]);
    }

    public function isEmpty(mixed $value): bool
    {
        return (bool) filter(static fn ($operator) => $value === $operator, ['', [], null]);
    }
}
