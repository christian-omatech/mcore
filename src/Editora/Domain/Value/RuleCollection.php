<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

use Omatech\Ecore\Editora\Domain\Value\Contracts\RulesListInterface;
use Omatech\Ecore\Editora\Domain\Value\Exceptions\InvalidRuleException;
use Omatech\Ecore\Editora\Domain\Value\Rules\Rule;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;

final class RuleCollection
{
    private array $rulesList;

    /** @var array<Rule> $rules */
    private array $rules;

    public function __construct(RulesListInterface $rulesList, array $rules)
    {
        $this->rulesList = $rulesList->get();
        $this->rules = map(function (mixed $condition, string $rule): Rule {
            return $this->find($rule, $condition);
        }, $rules);
    }

    private function find(string $rule, mixed $condition): Rule
    {
        $ruleClass = $this->rulesList[$rule] ?? InvalidRuleException::withRule($rule);
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
