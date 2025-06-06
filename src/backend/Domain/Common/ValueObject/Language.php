<?php

namespace App\Domain\Common\ValueObject;

final class Language
{
    private const SUPPORTED_LANGUAGES = ['ca', 'es', 'en', 'fr', 'it', 'pt'];

    private string $value;

    public function __construct(string $languageCode)
    {
        $languageCode = strtolower($languageCode);

        if (!in_array($languageCode, self::SUPPORTED_LANGUAGES, true)) {
            throw new \InvalidArgumentException("Unsupported language: $languageCode");
        }

        $this->value = $languageCode;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function is(string $languageCode): bool
    {
        return $this->value === strtolower($languageCode);
    }

    public static function default(): self
    {
        return new self('ca');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
