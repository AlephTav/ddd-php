<?php

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
     * @param callable $config
     * @return void
     */
    public static function set(callable $config): void
    {
        self::$config = $config;
    }

    /**
     * Returns the specified configuration value.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key = null, $default = null)
    {
        return (self::$config)($key, $default);
    }
}