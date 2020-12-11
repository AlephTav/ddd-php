<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;

/**
 * @method static static RU(string $method = null)
 * @method static static UK(string $method = null)
 * @method static static ES(string $method = null)
 * @method static static EN(string $method = null)
 */
class Language extends AbstractEnum
{
    private const RU = 'ru_RU';
    private const UK = 'uk_UA';
    private const ES = 'es_ES';
    private const EN = 'en_US';

    private string $locale;

    protected function __construct(string $locale)
    {
        parent::__construct();
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
