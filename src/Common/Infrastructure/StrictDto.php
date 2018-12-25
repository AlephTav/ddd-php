<?php

namespace AlephTools\DDD\Common\Infrastructure;

class StrictDto extends Dto
{
    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct($properties, true);
    }
}
