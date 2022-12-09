<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value;

final class Metadata
{
    private readonly string $attributeKey;
    private readonly string $language;
    private readonly array $rules;

    public function __construct(string $attributeKey, string $language, array $rules)
    {
        $this->attributeKey = $attributeKey;
        $this->language = $language;
        $this->rules = $rules;
    }

    public function attributeKey(): string
    {
        return $this->attributeKey;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function rules(): array
    {
        return $this->rules;
    }
}
