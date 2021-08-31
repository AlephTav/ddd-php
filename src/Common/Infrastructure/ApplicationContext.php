<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

final class ApplicationContext
{
    /**
     * The application context (DI container).
     *
     * @psalm-var callable(class-string|string|null,array):object
     */
    private static $context;

    /**
     * Sets the application context.
     *
     * @psalm-param callable(class-string|string|null,array):object $context
     */
    public static function set(callable $context): void
    {
        self::$context = $context;
    }

    /**
     * Resolves the given type from the container.
     *
     * @template T
     * @template P as class-string<T>|string|null
     * @param P $abstract
     * @psalm-return (P is class-string<T> ? T : object)
     */
    public static function get(string $abstract = null, array $parameters = [])
    {
        return (self::$context)($abstract, $parameters);
    }
}
