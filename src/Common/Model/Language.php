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
 * @method static static EN_GB(string $method = null)
 * @method static static EN_KE(string $method = null)
 * @method static static EN_NG(string $method = null)
 * @method static static AR_EG(string $method = null)
 * @method static static AR_AE(string $method = null)
 * @method static static AR_SA(string $method = null)

 * @method static static AR_BH(string $method = null)
 * @method static static ES_AR(string $method = null)
 * @method static static ES_VE(string $method = null)
 * @method static static ES_MX(string $method = null)
 * @method static static ES_BO(string $method = null)
 * @method static static ES_CL(string $method = null)
 * @method static static PT_BR(string $method = null)
 * @method static static PT_PT(string $method = null)
 * @method static static ET_EE(string $method = null)
 * @method static static LV_LV(string $method = null)
 * @method static static AR_KW(string $method = null)
 * @method static static AZ_AZ(string $method = null)
 * @method static static KA_GE(string $method = null)
 */
class Language extends AbstractEnum
{
    private const AR_KW = 'ar_KW';
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
    private const EN_GB = 'en_GB';
    private const EN_KE = 'en_KE';
    private const EN_NG = 'en_NG';
    private const AR_EG = 'ar_EG';
    private const AR_AE = 'ar_AE';
    private const AR_SA = 'ar_SA';

    private const AR_BH = 'ar_BH';
    private const ES_AR = 'es_AR';
    private const ES_VE = 'es_VE';
    private const ES_MX = 'es_MX';
    private const ES_BO = 'es_BO';
    private const ES_CL = 'es_CL';
    private const PT_BR = 'pt_BR';
    private const PT_PT = 'pt_PT';
    private const ET_EE = 'et_EE';
    private const LV_LV = 'lv_LV';
    private const AZ_AZ = 'az_AZ';
    private const KA_GE = 'ka_GE';

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
