<?php

namespace AlephTools\DDD\Common\Infrastructure;

final class ApplicationContext
{
    /**
     * The application context (DI container).
     *
     * @var callable
     */
    private static $context;

    /**
     * Sets the application context.
     *
     * @param callable $context
     * @return void
     */
    public static function set(callable $context): void
    {
        self::$context = $context;
    }

    /**
     * Resolves the given type from the container.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public static function get(string $abstract = null, array $parameters = [])
    {
        return (self::$context)($abstract, $parameters);
    }
}