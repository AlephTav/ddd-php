<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Enums\NamedEnum;

/**
 * @method static static FEMALE(string $method = null)
 * @method static static MALE(string $method = null)
 */
class Gender extends NamedEnum
{
    private const FEMALE = 'Female';
    private const MALE = 'Male';
}
