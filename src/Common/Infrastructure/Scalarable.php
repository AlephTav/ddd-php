<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

interface Scalarable
{
    public function toString(): string;

    public function toScalar(): mixed;
}
