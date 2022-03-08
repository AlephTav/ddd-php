<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

abstract class StrictDto extends Dto
{
    /**
     * @param array<string,mixed> $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct($properties, true, false);
    }
}
