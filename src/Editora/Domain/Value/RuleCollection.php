<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

use Omatech\Ecore\Editora\Domain\Value\Contracts\RulesListInterface;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\InvalidRuleException;
use Omatech\Ecore\Editora\Domain\Value\Rules\Rule;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;

class RuleCollection
{
    private array $rulesList = [];

    /** @var array<Rule> $rules */
    private array $rules;

    public function __construct(RulesListInterface $rulesList)
    {
        $this->rulesList = $rulesList->get();
    }

    public function addRules(array $rules): void
    {
        $this->rules = map(function (mixed $condition, string $rule) {
            return $this->find($rule, $condition);
        }, $rules);
    }

    private function find(string $rule, mixed $condition): Rule
    {
        if (! array_key_exists($rule, $this->rulesList)) {
            InvalidRuleException::withRule($rule);
        }
        $ruleClass = $this->rulesList[$rule];
        return new $ruleClass($condition);
    }

    public function validate(string $key, string $language, mixed $value): void
    {
        each(static fn (Rule $rule) => $rule->validate($key, $language, $value), $this->rules);
    }

    public function get(): array
    {
        return map(static fn (Rule $rule) => $rule->condition(), $this->rules);
    }
}
