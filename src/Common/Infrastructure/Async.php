<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

interface Async
{
    /**
     * Asynchronously executes (in background) a callback.
     */
    public function run(mixed $callback, array $params = [], int $delay = 0): void;

    /**
     * Asynchronously executes (in background) callbacks.
     */
    public function runBatch(mixed $callback, array $params = [], int $delay = 0): void;
}
