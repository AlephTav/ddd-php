<?php

namespace AlephTools\DDD\Common\Infrastructure;

interface Async
{
    /**
     * Asynchronously executes (in background) a callback.
     *
     * @param mixed $callback
     * @param array $params
     * @return void
     */
    public function run($callback, array $params = []): void;
}
