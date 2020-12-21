<?php

namespace AlephTools\DDD\Common\Infrastructure;

abstract class DynamicDto extends Dto
{
    public function __construct(array $properties = [], bool $strict = true)
    {
        parent::__construct($properties, $strict, true);
    }
}