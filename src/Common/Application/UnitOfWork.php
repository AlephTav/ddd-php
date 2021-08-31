<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application;

interface UnitOfWork
{
    /**
     * Execute some code in one transaction.
     *
     * @return mixed
     */
    public function execute(callable $callback);
}
