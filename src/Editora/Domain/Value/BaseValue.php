<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value;

abstract class BaseValue
{
    protected Metadata $metadata;
    protected mixed $value = null;
    protected Configuration $configuration;
    private RuleCollection $ruleCollection;

    public function __construct(string $attributeKey, string $language, array $properties)
    {
        $this->metadata = new Metadata($attributeKey, $language);
        $this->configuration = new Configuration($properties['configuration']);
        $this->ruleCollection = new RuleCollection(new Rules(), $properties['rules']);
    }

    abstract public function value(): mixed;

    public function validate(): void
    {
        $this->ruleCollection->validate(
            $this->metadata->attributeKey(),
            $this->metadata->language(),
            $this->value
        );
    }

    public function fill(mixed $value): void
    {
        $this->value = $value;
    }

    public function language(): string
    {
        return $this->metadata->language();
    }

    public function toArray(): array
    {
        return [
            'language' => $this->metadata->language(),
            'rules' => $this->ruleCollection->get(),
            'configuration' => $this->configuration->get(),
            'value' => $this->value(),
        ];
    }
}
