<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;

/**
 * @method static static RU(string $method = null)
 * @method static static UA(string $method = null)
 * @method static static EN(string $method = null)
 */
class Language extends AbstractEnum
{
    private const RU = 'ru_RU';
    private const UA = 'uk_UA';
    private const EN = 'en_US';

    private $locale;

    protected function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
