<?php declare(strict_types=1);

namespace Omatech\Ecore\Editora\Domain\Value;

abstract class BaseValue
{
    protected string $attributeKey;
    protected string $language;
    protected mixed $value = null;
    protected Configuration $configuration;
    private RuleCollection $ruleCollection;

    public function __construct(string $attributeKey, string $language, array $properties)
    {
        $this->configuration = new Configuration($properties['configuration']);
        $this->ruleCollection = new RuleCollection(new Rules(), $properties['rules']);
        $this->language = $language;
        $this->attributeKey = $attributeKey;
    }

    abstract public function value(): mixed;

    public function validate(): void
    {
        $this->ruleCollection->validate($this->attributeKey, $this->language, $this->value);
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
            'configuration' => $this->configuration->get(),
            'value' => $this->value(),
        ];
    }
}
