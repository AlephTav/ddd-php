<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

interface Scalarable
{
    public function toString(): string;

    /**
     * @return mixed
     */
    public function toScalar();
}
