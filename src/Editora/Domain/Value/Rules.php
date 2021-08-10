<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

use Omatech\Ecore\Editora\Domain\Value\Contracts\RulesListInterface;
use Omatech\Ecore\Editora\Domain\Value\Rules\LookupRule;
use Omatech\Ecore\Editora\Domain\Value\Rules\RequiredRule;

class Rules implements RulesListInterface
{
    public function get(): array
    {
        return [
            'required' => RequiredRule::class,
            'lookup' => LookupRule::class,
        ];
    }
}
