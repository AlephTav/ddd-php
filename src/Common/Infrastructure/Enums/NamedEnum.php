<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure\Enums;

/**
 * The base class for enums that associated with one string value.
 */
class NamedEnum extends AbstractEnum
{
    /**
     * The name associated with the enum value.
     *
     */
    protected string $name = '';

    /**
     * Constructor.
     *
     */
    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name that associated with the current enum value.
     *
     */
    public function getName(): string
    {
        return $this->name;
    }
}
