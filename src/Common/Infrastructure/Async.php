<?php

namespace AlephTools\DDD\Common\Infrastructure;

interface Async
{
    /**
     * Asynchronously executes (in background) a callback.
     *
     * @param callable $callback
     * @return void
     */
    public function run(callable $callback): void;
}
