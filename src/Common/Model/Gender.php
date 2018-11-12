<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Enums\NamedEnum;

/**
 * @method static Gender FEMALE(string $method = null)
 * @method static Gender MALE(string $method = null)
 */
class Gender extends NamedEnum
{
    public const FEMALE = 'Female';
    public const MALE = 'Male';
}