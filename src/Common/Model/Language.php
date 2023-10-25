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
 * @method static static AR_KW(string $method = null)
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
 * @method static static AZ_AZ(string $method = null)
 * @method static static KA_GE(string $method = null)
 * @method static static LT_LT(string $method = null)
 * @method static static KY(string $method = null)
 * @method static static FR_FR(string $method = null)
 * @method static static SI_LK(string $method = null)
 */
class Language extends AbstractEnum
{
    private const RU = ['ru_RU', 'русский'];
    private const UK = ['uk_UA', 'український'];
    private const ES = ['es_ES', 'español'];
    private const EN = ['en_US', 'english'];
    private const DE = ['de_DE', 'deutsch'];
    private const VI = ['vi_VN', 'tiếng việt'];
    private const KZ = ['kk_KZ', 'қазақ'];
    private const UZ = ['uz_UZ', "o'zbek"];
    private const CS = ['cs_CZ', 'čeština'];
    private const HU = ['hu_HU', 'magyar'];
    private const ID = ['id_ID', 'bahasa indonesia'];
    private const TJ = ['tg_TJ', 'тоҷикӣ'];
    private const KY = ['ky_KG', 'кыргызча'];
    private const EN_GB = ['en_GB', 'english (GB)'];
    private const EN_KE = ['en_KE', 'english (KE)'];
    private const EN_NG = ['en_NG', 'english (NG)'];
    private const AR_EG = ['ar_EG', 'عرب (EG)'];
    private const AR_AE = ['ar_AE', 'عرب (AE)'];
    private const AR_SA = ['ar_SA', 'عرب (SA)'];
    private const AR_KW = ['ar_KW', 'عرب (KW)'];
    private const AR_BH = ['ar_BH', 'عرب (BH)'];
    private const ES_AR = ['es_AR', 'español (AR)'];
    private const ES_VE = ['es_VE', 'español (VE)'];
    private const ES_MX = ['es_MX', 'español (MX)'];
    private const ES_BO = ['es_BO', 'español (BO)'];
    private const ES_CL = ['es_CL', 'español (CL)'];
    private const PT_BR = ['pt_BR', 'portugués (BR)'];
    private const PT_PT = ['pt_PT', 'portugués'];
    private const ET_EE = ['et_EE', 'eesti keel'];
    private const LV_LV = ['lv_LV', 'latviski'];
    private const AZ_AZ = ['az_AZ', 'azərbaycan'];
    private const KA_GE = ['ka_GE', 'ქართული'];
    private const LT_LT = ['lt_LT', 'lietuvių'];
    private const FR_FR = ['fr_FR', 'français'];
    private const SI_LK = ['si_LK', 'සිංහල'];

    private string $locale;
    private string $name;

    protected function __construct(string $locale, string $name)
    {
        parent::__construct();
        $this->locale = $locale;
        $this->name = $name;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
