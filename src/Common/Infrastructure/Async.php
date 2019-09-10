<?php

namespace AlephTools\DDD\Common\Infrastructure;

interface Async
{
    /**
     * Asynchronously executes (in background) a callback.
     *
     * @param callable $callback
     * @param array $params
     * @return void
     */
    public function run(callable $callback, array $params = []): void;
}
