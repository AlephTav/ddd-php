<?php

namespace AlephTools\DDD\Common\Infrastructure\Enums;

/**
 * The base class for enums that associated with one string value.
 */
class NamedEnum extends AbstractEnum
{
    /**
     * The name associated with the enum value.
     *
     * @var string
     */
    protected string $name = '';

    /**
     * Constructor.
     *
     * @param string $name
     */
    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name that associated with the current enum value.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}