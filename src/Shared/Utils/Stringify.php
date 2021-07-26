<?php declare(strict_types=1);

namespace Omatech\Ecore\Shared\Utils;

use Cocur\Slugify\Slugify;

class Stringify
{
    private static ?Stringify $instance = null;
    private Slugify $slugify;

    private function __construct()
    {
        $this->slugify = new Slugify();
    }

    public static function getInstance(): Stringify
    {
        if (! self::$instance) {
            self::$instance = new Stringify();
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
}
