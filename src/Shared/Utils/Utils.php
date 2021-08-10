<?php declare(strict_types=1);

namespace Omatech\Mcore\Shared\Utils;

use Cocur\Slugify\Slugify;

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

    public function slug(string $string): string
    {
        return $this->slugify->slugify($string, [
            'regexp' => '/(?<=[[:^upper:]])(?=[[:upper:]])/',
            'lowercase_after_regexp' => true,
        ]);
    }

    public function isEmpty(mixed $value): bool
    {
        return $value === null ||
            $value === '' ||
            $value === [];
    }
}
