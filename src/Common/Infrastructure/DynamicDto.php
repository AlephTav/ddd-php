<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

abstract class DynamicDto extends Dto
{
    /**
     * @param array<string,mixed> $properties
     */
    public function __construct(array $properties = [], bool $strict = true)
    {
        parent::__construct($properties, $strict, true);
    }
}
