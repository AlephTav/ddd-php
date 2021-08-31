<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure\Localization;

interface Translator
{
    /**
     * Get the translation for the given key.
     *
     * @param string $key The string to translate or its unique identifier.
     * @param array $replacement The key-value pairs to replace substrings in the translated string.
     * @return string|array
     */
    public function get(string $key, array $replacement = [], string $locale = null);
}
