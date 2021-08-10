<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

abstract class BaseValue
{
    protected mixed $value = null;
    private string $key;
    private string $language;
    private RuleCollection $ruleCollection;
    private array $configuration;

    public function __construct(string $key, string $language, array $properties)
    {
        $this->ruleCollection = new RuleCollection(new Rules());
        $this->ruleCollection->addRules($properties['rules']);
        $this->language = $language;
        $this->configuration = $properties['configuration'];
        $this->key = $key;
    }

    abstract public function value(): mixed;

    public function validate(): void
    {
        $this->ruleCollection->validate($this->key, $this->language, $this->value);
    }

    public function fill(mixed $value): void
    {
        $this->value = $value;
    }

    public function language(): string
    {
        return $this->language;
    }

    protected function configuration(): array
    {
        return $this->configuration;
    }

    protected function key(): string
    {
        return $this->key;
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
