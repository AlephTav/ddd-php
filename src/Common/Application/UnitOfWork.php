<?php

namespace AlephTools\DDD\Common\Application;

interface UnitOfWork
{
    /**
     * Execute some code in one transaction.
     *
     * @param callable $callback
     * @return mixed
     */
    public function execute(callable $callback);
}
