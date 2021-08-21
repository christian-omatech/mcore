<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value;

use Omatech\Mcore\Editora\Domain\Value\Exceptions\InvalidRuleException;
use Omatech\Mcore\Editora\Domain\Value\Rules\Rule;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\map;

final class RuleCollection
{
    /** @var array<Rule> $rules */
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = map(static function (mixed $condition, string $rule): Rule {
            $class = first(filter(static fn ($class) => class_exists($class), [
                'Omatech\\Mcore\\Editora\\Domain\\Value\\Rules\\' . ucfirst($rule),
                $rule,
            ])) ?? InvalidRuleException::withRule($rule);
            return new $class($condition);
        }, $rules);
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
