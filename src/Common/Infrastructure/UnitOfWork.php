<?php

namespace AlephTools\DDD\Common\Infrastructure;

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