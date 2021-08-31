<?php

declare(strict_types=1);

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
     */
    public function equals($other): bool;

    /**
     * Generates a hash value for this object.
     *
     */
    public function hash(): string;
}
