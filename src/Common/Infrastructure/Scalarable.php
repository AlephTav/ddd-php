<?php

namespace AlephTools\DDD\Common\Infrastructure;

interface Scalarable
{
    public function toString(): string;

    public function toScalar();
}