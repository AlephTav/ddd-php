<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;

/**
 * Implementation of currencies according to ISO 4217.
 *
 * @method static static AED(string $method = null)
 * @method static static AFN(string $method = null)
 * @method static static ALL(string $method = null)
 * @method static static AMD(string $method = null)
 * @method static static ANG(string $method = null)
 * @method static static AOA(string $method = null)
 * @method static static ARS(string $method = null)
 * @method static static AWG(string $method = null)
 * @method static static AZN(string $method = null)
 * @method static static BAM(string $method = null)
 * @method static static BBD(string $method = null)
 * @method static static BBT(string $method = null)
 * @method static static BGN(string $method = null)
 * @method static static BHD(string $method = null)
 * @method static static BIF(string $method = null)
 * @method static static BMD(string $method = null)
 * @method static static BND(string $method = null)
 * @method static static BOB(string $method = null)
 * @method static static BOV(string $method = null)
 * @method static static BRL(string $method = null)
 * @method static static BSD(string $method = null)
 * @method static static BTN(string $method = null)
 * @method static static BWP(string $method = null)
 * @method static static BYN(string $method = null)
 * @method static static BZD(string $method = null)
 * @method static static CAD(string $method = null)
 * @method static static CDF(string $method = null)
 * @method static static CHE(string $method = null)
 * @method static static CHF(string $method = null)
 * @method static static CHW(string $method = null)
 * @method static static CLF(string $method = null)
 * @method static static CLP(string $method = null)
 * @method static static CNY(string $method = null)
 * @method static static COP(string $method = null)
 * @method static static COU(string $method = null)
 * @method static static CRC(string $method = null)
 * @method static static CUC(string $method = null)
 * @method static static CUP(string $method = null)
 * @method static static CVE(string $method = null)
 * @method static static CZK(string $method = null)
 * @method static static DJF(string $method = null)
 * @method static static DKK(string $method = null)
 * @method static static DOP(string $method = null)
 * @method static static DZD(string $method = null)
 * @method static static EGP(string $method = null)
 * @method static static ERN(string $method = null)
 * @method static static ETB(string $method = null)
 * @method static static EUR(string $method = null)
 * @method static static FJD(string $method = null)
 * @method static static FKP(string $method = null)
 * @method static static GBP(string $method = null)
 * @method static static GEL(string $method = null)
 * @method static static GHS(string $method = null)
 * @method static static GIP(string $method = null)
 * @method static static GMD(string $method = null)
 * @method static static GNF(string $method = null)
 * @method static static GTQ(string $method = null)
 * @method static static GYD(string $method = null)
 * @method static static HKD(string $method = null)
 * @method static static HNL(string $method = null)
 * @method static static HRK(string $method = null)
 * @method static static HTG(string $method = null)
 * @method static static HUF(string $method = null)
 * @method static static IDR(string $method = null)
 * @method static static ILS(string $method = null)
 * @method static static INR(string $method = null)
 * @method static static IQD(string $method = null)
 * @method static static IRR(string $method = null)
 * @method static static ISK(string $method = null)
 * @method static static JMD(string $method = null)
 * @method static static JOD(string $method = null)
 * @method static static JPY(string $method = null)
 * @method static static KES(string $method = null)
 * @method static static KGS(string $method = null)
 * @method static static KHR(string $method = null)
 * @method static static KMF(string $method = null)
 * @method static static KPW(string $method = null)
 * @method static static KRW(string $method = null)
 * @method static static KWD(string $method = null)
 * @method static static KYD(string $method = null)
 * @method static static KZT(string $method = null)
 * @method static static LAK(string $method = null)
 * @method static static LBP(string $method = null)
 * @method static static LKR(string $method = null)
 * @method static static LRD(string $method = null)
 * @method static static LSL(string $method = null)
 * @method static static LYD(string $method = null)
 * @method static static MAD(string $method = null)
 * @method static static MDL(string $method = null)
 * @method static static MGA(string $method = null)
 * @method static static MKD(string $method = null)
 * @method static static MMK(string $method = null)
 * @method static static MNT(string $method = null)
 * @method static static MOP(string $method = null)
 * @method static static MRU(string $method = null)
 * @method static static MUR(string $method = null)
 * @method static static MVR(string $method = null)
 * @method static static MWK(string $method = null)
 * @method static static MXN(string $method = null)
 * @method static static MXV(string $method = null)
 * @method static static MYR(string $method = null)
 * @method static static MZN(string $method = null)
 * @method static static NAD(string $method = null)
 * @method static static NGN(string $method = null)
 * @method static static NIO(string $method = null)
 * @method static static NOK(string $method = null)
 * @method static static NPR(string $method = null)
 * @method static static NZD(string $method = null)
 * @method static static OMR(string $method = null)
 * @method static static PAB(string $method = null)
 * @method static static PEN(string $method = null)
 * @method static static PGK(string $method = null)
 * @method static static PHP(string $method = null)
 * @method static static PKR(string $method = null)
 * @method static static PLN(string $method = null)
 * @method static static PYG(string $method = null)
 * @method static static QAR(string $method = null)
 * @method static static RON(string $method = null)
 * @method static static RSD(string $method = null)
 * @method static static RUB(string $method = null)
 * @method static static RWF(string $method = null)
 * @method static static SAR(string $method = null)
 * @method static static SBD(string $method = null)
 * @method static static SCR(string $method = null)
 * @method static static SDG(string $method = null)
 * @method static static SEK(string $method = null)
 * @method static static SGD(string $method = null)
 * @method static static SHP(string $method = null)
 * @method static static SLL(string $method = null)
 * @method static static SOS(string $method = null)
 * @method static static SRD(string $method = null)
 * @method static static SSP(string $method = null)
 * @method static static STN(string $method = null)
 * @method static static SVC(string $method = null)
 * @method static static SYP(string $method = null)
 * @method static static SZL(string $method = null)
 * @method static static THB(string $method = null)
 * @method static static TJS(string $method = null)
 * @method static static TMT(string $method = null)
 * @method static static TND(string $method = null)
 * @method static static TOP(string $method = null)
 * @method static static TRY(string $method = null)
 * @method static static TTD(string $method = null)
 * @method static static TWD(string $method = null)
 * @method static static TZS(string $method = null)
 * @method static static UAH(string $method = null)
 * @method static static UGX(string $method = null)
 * @method static static USD(string $method = null)
 * @method static static USN(string $method = null)
 * @method static static UYI(string $method = null)
 * @method static static UYU(string $method = null)
 * @method static static UYW(string $method = null)
 * @method static static UZS(string $method = null)
 * @method static static VES(string $method = null)
 * @method static static VND(string $method = null)
 * @method static static VUV(string $method = null)
 * @method static static WST(string $method = null)
 * @method static static XAF(string $method = null)
 * @method static static XCD(string $method = null)
 * @method static static XOF(string $method = null)
 * @method static static XPF(string $method = null)
 * @method static static YER(string $method = null)
 * @method static static ZAR(string $method = null)
 * @method static static ZMW(string $method = null)
 * @method static static ZWL(string $method = null)
 */
class Currency extends AbstractEnum
{
    private const AED = ['United Arab Emirates dirham', '784', 2];
    private const AFN = ['Afghan afghani', '971', 2];
    private const ALL = ['Albanian lek', '008', 2];
    private const AMD = ['Armenian dram', '051', 2];
    private const ANG = ['Netherlands Antillean guilder', '532', 2];
    private const AOA = ['Angolan kwanza', '973', 2];
    private const ARS = ['Argentine peso', '032', 2];
    private const AUD = ['Australian dollar', '036', 2];
    private const AWG = ['Aruban florin', '533', 2];
    private const AZN = ['Azerbaijani manat', '944', 2];
    private const BAM = ['Bosnia and Herzegovina convertible mark', '977', 2];
    private const BBD = ['Barbados dollar', '052', 2];
    private const BDT = ['Bangladeshi taka', '050', 2];
    private const BGN = ['Bulgarian lev', '975', 2];
    private const BHD = ['Bahraini dinar', '048', 3];
    private const BIF = ['Burundian franc', '108', 0];
    private const BMD = ['Bermudian dollar', '060', 2];
    private const BND = ['Brunei dollar', '096', 2];
    private const BOB = ['Boliviano', '068', 2];
    private const BOV = ['Bolivian Mvdol (funds code)', '984', 2];
    private const BRL = ['Brazilian real', '986', 2];
    private const BSD = ['Bahamian dollar', '044', 2];
    private const BTN = ['Bhutanese ngultrum', '064', 2];
    private const BWP = ['Botswana pula', '072', 2];
    private const BYN = ['Belarusian ruble', '933', 2];
    private const BZD = ['Belize dollar', '084', 2];
    private const CAD = ['Canadian dollar', '124', 2];
    private const CDF = ['Congolese franc', '976', 2];
    private const CHE = ['WIR Euro (complementary currency)', '947', 2];
    private const CHF = ['Swiss franc', '756', 2];
    private const CHW = ['WIR Franc (complementary currency)', '948', 2];
    private const CLF = ['Unidad de Fomento (funds code)', '990', 4];
    private const CLP = ['Chilean peso', '152', 0];
    private const CNY = ['Renminbi (Chinese) yuan', '156', 2];
    private const COP = ['Colombian peso', '170', 2];
    private const COU = ['Unidad de Valor Real (UVR) (funds code)', '970', 2];
    private const CRC = ['Costa Rican colon', '188', 2];
    private const CUC = ['Cuban convertible peso', '931', 2];
    private const CUP = ['Cuban peso', '192', 2];
    private const CVE = ['Cape Verde escudo', '132', 2];
    private const CZK = ['Czech koruna', '203', 2];
    private const DJF = ['Djiboutian franc', '262', 0];
    private const DKK = ['Danish krone', '208', 2];
    private const DOP = ['Dominican peso', '214', 2];
    private const DZD = ['Algerian dinar', '012', 2];
    private const EGP = ['Egyptian pound', '818', 2];
    private const ERN = ['Eritrean nakfa', '232', 2];
    private const ETB = ['Ethiopian birr', '230', 2];
    private const EUR = ['Euro', '978', 2];
    private const FJD = ['Fiji dollar', '242', 2];
    private const FKP = ['Falkland Islands pound', '238', 2];
    private const GBP = ['Pound sterling', '826', 2];
    private const GEL = ['Georgian lari', '981', 2];
    private const GHS = ['Ghanaian cedi', '936', 2];
    private const GIP = ['Gibraltar pound', '292', 2];
    private const GMD = ['Gambian dalasi', '270', 2];
    private const GNF = ['Guinean franc', '324', 0];
    private const GTQ = ['Guatemalan quetzal', '320', 2];
    private const GYD = ['Guyanese dollar', '328', 2];
    private const HKD = ['Hong Kong dollar', '344', 2];
    private const HNL = ['Honduran lempira', '340', 2];
    private const HRK = ['Croatian kuna', '191', 2];
    private const HTG = ['Haitian gourde', '332', 2];
    private const HUF = ['Hungarian forint', '348', 2];
    private const IDR = ['Indonesian rupiah', '360', 2];
    private const ILS = ['Israeli new shekel', '376', 2];
    private const INR = ['Indian rupee', '356', 2];
    private const IQD = ['Iraqi dinar', '368', 3];
    private const IRR = ['Iranian rial', '364', 2];
    private const ISK = ['Icelandic króna', '352', 0];
    private const JMD = ['Jamaican dollar', '388', 2];
    private const JOD = ['Jordanian dinar', '400', 3];
    private const JPY = ['Japanese yen', '392', 0];
    private const KES = ['Kenyan shilling', '404', 2];
    private const KGS = ['Kyrgyzstani som', '417', 2];
    private const KHR = ['Cambodian riel', '116', 2];
    private const KMF = ['Comoro franc', '174', 0];
    private const KPW = ['North Korean won', '408', 2];
    private const KRW = ['South Korean won', '410', 0];
    private const KWD = ['Kuwaiti dinar', '414', 3];
    private const KYD = ['Cayman Islands dollar', '136', 2];
    private const KZT = ['Kazakhstani tenge', '398', 2];
    private const LAK = ['Lao kip', '418', 2];
    private const LBP = ['Lebanese pound', '422', 2];
    private const LKR = ['Sri Lankan rupee', '144', 2];
    private const LRD = ['Liberian dollar', '430', 2];
    private const LSL = ['Lesotho loti', '426', 2];
    private const LYD = ['Libyan dinar', '434', 3];
    private const MAD = ['Moroccan dirham', '504', 2];
    private const MDL = ['Moldovan leu', '498', 2];
    private const MGA = ['Malagasy ariary', '969', 2];
    private const MKD = ['Macedonian denar', '807', 2];
    private const MMK = ['Myanmar kyat', '104', 2];
    private const MNT = ['Mongolian tögrög', '496', 2];
    private const MOP = ['Macanese pataca', '446', 2];
    private const MRU = ['Mauritanian ouguiya', '929', 2];
    private const MUR = ['Mauritian rupee', '480', 2];
    private const MVR = ['Maldivian rufiyaa', '462', 2];
    private const MWK = ['Malawian kwacha', '454', 2];
    private const MXN = ['Mexican peso', '484', 2];
    private const MXV = ['Mexican Unidad de Inversion (UDI) (funds code)', '979', 2];
    private const MYR = ['Malaysian ringgit', '458', 2];
    private const MZN = ['Mozambican metical', '943', 2];
    private const NAD = ['Namibian dollar', '516', 2];
    private const NGN = ['Nigerian naira', '566', 2];
    private const NIO = ['Nicaraguan córdoba', '558', 2];
    private const NOK = ['Norwegian krone', '578', 2];
    private const NPR = ['Nepalese rupee', '524', 2];
    private const NZD = ['New Zealand dollar', '554', 2];
    private const OMR = ['Omani rial', '512', 3];
    private const PAB = ['Panamanian balboa', '590', 2];
    private const PEN = ['Peruvian sol', '604', 2];
    private const PGK = ['Papua New Guinean kina', '598', 2];
    private const PHP = ['Philippine peso', '608', 2];
    private const PKR = ['Pakistani rupee', '586', 2];
    private const PLN = ['Polish złoty', '985', 2];
    private const PYG = ['Paraguayan guaraní', '600', 0];
    private const QAR = ['Qatari riyal', '634', 2];
    private const RON = ['Romanian leu', '946', 2];
    private const RSD = ['Serbian dinar', '941', 2];
    private const RUB = ['Russian ruble', '643', 2];
    private const RWF = ['Rwandan franc', '646', 0];
    private const SAR = ['Saudi riyal', '682', 2];
    private const SBD = ['Solomon Islands dollar', '090', 2];
    private const SCR = ['Seychelles rupee', '690', 2];
    private const SDG = ['Sudanese pound', '938', 2];
    private const SEK = ['Swedish krona/kronor', '752', 2];
    private const SGD = ['Singapore dollar', '702', 2];
    private const SHP = ['Saint Helena pound', '654', 2];
    private const SLL = ['Sierra Leonean leone', '694', 2];
    private const SOS = ['Somali shilling', '706', 2];
    private const SRD = ['Surinamese dollar', '968', 2];
    private const SSP = ['South Sudanese pound', '728', 2];
    private const STN = ['São Tomé and Príncipe dobra', '930', 2];
    private const SVC = ['Salvadoran colón', '222', 2];
    private const SYP = ['Syrian pound', '760', 2];
    private const SZL = ['Swazi lilangeni', '748', 2];
    private const THB = ['Thai baht', '764', 2];
    private const TJS = ['Tajikistani somoni', '972', 2];
    private const TMT = ['Turkmenistan manat', '934', 2];
    private const TND = ['Tunisian dinar', '788', 3];
    private const TOP = ['Tongan paʻanga', '776', 2];
    private const TRY = ['Turkish lira', '949', 2];
    private const TTD = ['Trinidad and Tobago dollar', '780', 2];
    private const TWD = ['New Taiwan dollar', '901', 2];
    private const TZS = ['Tanzanian shilling', '834', 2];
    private const UAH = ['Ukrainian hryvnia', '980', 2];
    private const UGX = ['Ugandan shilling', '800', 0];
    private const USD = ['United States dollar', '840', 2];
    private const USN = ['United States dollar (next day) (funds code)', '997', 2];
    private const UYI = ['Uruguay Peso', '940', 0];
    private const UYU = ['Uruguayan peso', '858', 2];
    private const UYW = ['Unidad previsional', '927', 4];
    private const UZS = ['Uzbekistan som', '860', 2];
    private const VES = ['Venezuelan bolívar', '928', 2];
    private const VND = ['Vietnamese đồng', '704', 0];
    private const VUV = ['Vanuatu vatu', '548', 0];
    private const WST = ['Samoan tala', '882', 2];
    private const XAF = ['CFA franc BEAC', '950', 0];
    private const XCD = ['East Caribbean dollar', '951', 2];
    private const XOF = ['CFA franc BCEAO', '952', 0];
    private const XPF = ['CFP franc (franc Pacifique)', '953', 0];
    private const YER = ['Yemeni rial', '886', 2];
    private const ZAR = ['South African rand', '710', 2];
    private const ZMW = ['Zambian kwacha', '967', 2];
    private const ZWL = ['Zimbabwean dollar', '932', 2];

    private string $name;
    private string $numericCode;
    private int $subunits;

    protected function __construct(string $name, string $numericCode, int $subunits)
    {
        $this->name = $name;
        $this->numericCode = $numericCode;
        $this->subunits = $subunits;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumericCode(): string
    {
        return $this->numericCode;
    }

    /**
     * Returns number of digits after the decimal separator.
     *
     * @return int
     */
    public function getSubunits(): int
    {
        return $this->subunits;
    }
}
