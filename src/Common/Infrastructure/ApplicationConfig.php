<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

final class ApplicationConfig
{
    /**
     * The application config.
     *
     * @var callable
     */
    private static $config;

    /**
     * Sets the application config.
     *
     */
    public static function set(callable $config): void
    {
        self::$config = $config;
    }

    /**
     * Returns the specified configuration value.
     *
     */
    public static function get(string $key = null, mixed $default = null): mixed
    {
        return (self::$config)($key, $default);
    }
}
