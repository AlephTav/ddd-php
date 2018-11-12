<?php

namespace AlephTools\DDD\Common\Infrastructure;

interface UnitOfWork
{
    public function execute(callable $callback);
}