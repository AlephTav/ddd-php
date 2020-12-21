<?php

namespace AlephTools\DDD\Common\Infrastructure;

abstract class DynamicWeakDto extends DynamicDto
{
    public function __construct(array $properties = [])
    {
        parent::__construct($properties, false);
    }
}