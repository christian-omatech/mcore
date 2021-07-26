<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

abstract class BaseValue
{
    protected mixed $value = null;
    private RuleCollection $ruleCollection;
    private array $configuration;
    private string $language;

    public function __construct(string $language, array $properties)
    {
        $this->ruleCollection = new RuleCollection(new Rules());
        $this->ruleCollection->addRules($properties['rules']);
        $this->language = $language;
        $this->configuration = $properties['configuration'];
    }

    abstract public function value(): mixed;

    public function validate(string $key): void
    {
        $this->ruleCollection->validate($key, $this->language, $this->value);
    }

    public function fill(mixed $value): void
    {
        $this->value = $value;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function toArray(): array
    {
        return [
            'language' => $this->language,
            'rules' => $this->ruleCollection->get(),
            'configuration' => $this->configuration,
            'value' => $this->value(),
        ];
    }
}
