<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

interface Async
{
    /**
     * Asynchronously executes (in background) a callback.
     *
     * @param mixed $callback
     */
    public function run($callback, array $params = []): void;
}
