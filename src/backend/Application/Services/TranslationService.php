<?php

namespace App\Application\Services;

use App\Domain\Common\ValueObject\Language;

class TranslationService
{
    private array $translations;

    public function __construct(Language $language)
    {
        $langCode = $language->value(); // "es", "ca", etc.
        $path = __DIR__ . '/../../resources/lang/' . $langCode . '.php';

        if (file_exists($path)) {
            $this->translations = require $path;
        } else {
            $this->translations = [];
        }
    }

    /**
     * Obtiene la traducción para una clave en formato "seccion.clave"
     * Ejemplo: translate('header.angles')
     */
    public function translate(string $key, string $default = ''): string
    {
        $keys = explode('.', $key);
        $value = $this->translations;

        foreach ($keys as $k) {
            if (is_array($value) && array_key_exists($k, $value)) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return is_string($value) ? $value : $default;
    }

    /**
     * Devuelve todas las traducciones (por si quieres pasar el array completo a la vista)
     */
    public function getAll(): array
    {
        return $this->translations;
    }
}
