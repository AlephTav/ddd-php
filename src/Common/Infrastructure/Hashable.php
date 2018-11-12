<?php

namespace AlephTools\DDD\Common\Infrastructure;

/**
 * Allows to compare objects.
 */
interface Hashable
{
    /**
     * Compares two objects.
     *
     * @param mixed $other
     * @return bool
     */
    public function equals($other): bool;

    /**
     * Generates a hash value for this object.
     *
     * @return string
     */
    public function hash(): string;
}