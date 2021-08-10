<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value;

use Omatech\Mcore\Editora\Domain\Value\Contracts\RulesListInterface;
use Omatech\Mcore\Editora\Domain\Value\Rules\RequiredRule;

class Rules implements RulesListInterface
{
    public function get(): array
    {
        return [
            'required' => RequiredRule::class,
        ];
    }
}
