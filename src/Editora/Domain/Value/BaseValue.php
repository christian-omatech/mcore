<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Value;

abstract class BaseValue
{
    protected ?int $id = null;
    protected mixed $value = null;
    protected array $extraData = [];
    protected Configuration $configuration;
    private Metadata $metadata;

    public function __construct(string $attributeKey, string $language, array $properties)
    {
        $this->metadata = new Metadata($attributeKey, $language, $properties['rules']);
        $this->configuration = new Configuration($properties['configuration']);
    }

    abstract public function value(): mixed;

    public function fill(array $value): void
    {
        $this->value = $value['value'];
        $this->extraData = $value['extraData'] ?? $this->extraData;
        $this->id = $value['id'] ?? $this->id;
    }

    public function language(): string
    {
        return $this->metadata->language();
    }

    public function rules(): array
    {
        return $this->metadata->rules();
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function key(): string
    {
        return $this->metadata->attributeKey();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'language' => $this->metadata->language(),
            'rules' => $this->metadata->rules(),
            'configuration' => $this->configuration->get(),
            'value' => $this->value,
            'extraData' => $this->extraData,
        ];
    }
}
