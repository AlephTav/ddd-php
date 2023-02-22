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
 * @method static static TJ(string $method = null)
 * @method static static GB(string $method = null)
 * @method static static KE(string $method = null)
 * @method static static NG(string $method = null)
 * @method static static EG(string $method = null)
 * @method static static AE(string $method = null)
 * @method static static SA(string $method = null)
 * @method static static KW(string $method = null)
 * @method static static BH(string $method = null)
 * @method static static AR(string $method = null)
 * @method static static VE(string $method = null)
 * @method static static MX(string $method = null)
 * @method static static BO(string $method = null)
 * @method static static CL(string $method = null)
 * @method static static BR(string $method = null)
 * @method static static PT(string $method = null)
 * @method static static EE(string $method = null)
 * @method static static LV(string $method = null)
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
    private const TJ = 'tg_TJ';
    private const GB = 'en_GB';
    private const KE = 'en_KE';
    private const NG = 'en_NG';
    private const EG = 'ar_EG';
    private const AE = 'ar_AE';
    private const SA = 'ar_SA';
    private const KW = 'ar_KW';
    private const BH = 'ar_BH';
    private const AR = 'es_AR';
    private const VE = 'es_VE';
    private const MX = 'es_MX';
    private const BO = 'es_BO';
    private const CL = 'es_CL';
    private const BR = 'pt_BR';
    private const PT = 'pt_PT';
    private const EE = 'et_EE';
    private const LV = 'lv_LV';

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
