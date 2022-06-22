<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;

/**
 * @method static static RU(string $method = null)
 * @method static static UK(string $method = null)
 * @method static static ES(string $method = null)
 * @method static static EN(string $method = null)
 * @method static static DE(string $method = null)
 * @method static static VI(string $method = null)
 * @method static static KZ(string $method = null)
 * @method static static UZ(string $method = null)
 * @method static static CS(string $method = null)
 * @method static static HU(string $method = null)
 * @method static static ID(string $method = null)
 */
class Language extends AbstractEnum
{
    private const RU = 'ru_RU';
    private const UK = 'uk_UA';
    private const ES = 'es_ES';
    private const EN = 'en_US';
    private const DE = 'de_DE';
    private const VI = 'vi_VN';
    private const KZ = 'kk_KZ';
    private const UZ = 'uz_UZ';
    private const CS = 'cs_CZ';
    private const HU = 'hu_HU';
    private const ID = 'id_ID';

    private string $locale;

    protected function __construct(string $locale)
    {
        parent::__construct();
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
