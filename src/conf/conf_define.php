<?php
//============================================
// conf_define.php
//============================================
//+++++++++++++++++++++++++++++
// 定数宣言
//+++++++++++++++++++++++++++++
// バイト数値
define('SYSTEM_BYTE_SIZE', 1024);

// キャリア
define('SYSTEM_CARRIER_PC', 0);
define('SYSTEM_CARRIER_DOCOMO', 1);
define('SYSTEM_CARRIER_AU', 2);
define('SYSTEM_CARRIER_KDDI', 2);
define('SYSTEM_CARRIER_SOFTBANK', 3);
define('SYSTEM_CARRIER_WILLCOM', 4);

// ブログ
define('SYSTEM_BLOG_AMEBA', 1); // AmeBlog
define('SYSTEM_BLOG_EXCITE', 2); // ExciteBlog
define('SYSTEM_BLOG_FC2', 3); // FC2
define('SYSTEM_BLOG_LIVEDOOR', 4); // ライブドア
define('SYSTEM_BLOG_MBTYPE', 5); // MovableType
define('SYSTEM_BLOG_WORDPRESS', 6); // Wordpress
// 時間
define('SYSTEM_TIME_1MINUTES', 60); // 1分
define('SYSTEM_TIME_1HOUR', 3600); // 1時間
define('SYSTEM_TIME_1DAY', SYSTEM_TIME_1HOUR * 24); // 1日
define('SYSTEM_TIME_1WEEK', SYSTEM_TIME_1DAY * 7); // 1週間
define('SYSTEM_TIME_1MONTH', SYSTEM_TIME_1DAY * 30); // 1月
define('SYSTEM_TIME_1YEAR', SYSTEM_TIME_1DAY * 365); // 1年

/**
 * Class class_define
 */
class class_carrer{
    /**
     * @var array : DOCOMO
     */
    static $CIDR_DOCOMO = array(
        '210.153.84.0/24',
        '210.136.161.0/24',
        '210.153.86.0/24',
        '124.146.174.0/24',
        '124.146.175.0/24',
        '202.229.176.0/24',
        '202.229.177.0/24',
        '202.229.178.0/24',
        // 2011/02より
        '202.229.179.0/24',
        '111.89.188.0/24',
        // 2011/07より
        '111.89.189.0/24',
        '111.89.190.0/24',
        '111.89.191.0/24'
    );

    /**
     * @var array : AU
     */
    static $CIDR_AU = array(
        '210.230.128.224/28',
        '121.111.227.160/27',
        '61.117.1.0/28',
        '219.108.158.0/27',
        '219.125.146.0/28',
        '61.117.2.32/29',
        '61.117.2.40/29',
        '219.108.158.40/29',
        '219.125.148.0/25',
        '222.5.63.0/25',
        '222.5.63.128/25',
        '222.5.62.128/25',
        '59.135.38.128/25',
        '219.108.157.0/25',
        '219.125.145.0/25',
        '121.111.231.0/25',
        '121.111.227.0/25',
        '118.152.214.192/26',
        '118.159.131.0/25',
        '118.159.133.0/25',
        '118.159.132.160/27',
        '111.86.142.0/26',
        '111.86.141.64/26',
        '111.86.141.128/26',
        '111.86.141.192/26',
        '118.159.133.192/26',
        '111.86.143.192/27',
        '111.86.143.224/27',
        '111.86.147.0/27',
        '111.86.142.128/27',
        '111.86.142.160/27',
        '111.86.142.192/27',
        '111.86.142.224/27',
        '111.86.143.0/27',
        '111.86.143.32/27',
        '111.86.147.32/27',
        '111.86.147.64/27',
        '111.86.147.96/27',
        '111.86.147.128/27',
        '111.86.147.160/27',
        '111.86.147.192/27',
        '111.86.147.224/27'
    );
    /**
     * @var array : SOFTBANK
     */
    static $CIDR_SOFTBANK = array(
        '123.108.237.0/27',
        '202.253.96.224/27',
        '210.146.7.192/26',
        '210.175.1.128/25'
    );

    /**
     * @param $career
     * @return array|null
     */
    static public function getCidr($career){
        $arr = self::getCidrAll();
        if(isset($arr[$career])){
            return $arr[$career];
        }
        return null;
    }

    /**
     * @return array
     */
    static public function getCidrAll(){
        $arr = array();
        $arr[SYSTEM_CARRIER_DOCOMO] = self::$CIDR_DOCOMO;
        $arr[SYSTEM_CARRIER_AU] = self::$CIDR_AU;
        $arr[SYSTEM_CARRIER_SOFTBANK] = self::$CIDR_SOFTBANK;
        return $arr;
    }

    /**
     * @var array : NTT ドコモ
     */
    static $DOMAIN_DOCOMO = array(
        'docomo.ne.jp',
    );
    /**
     * @var array : KDDI au / TU-KA
     */
    static $DOMAIN_AU = array(
        'ezweb.ne.jp',
        'yy.ezweb.ne.jp',
    );
    /**
     * @var array : ソフトバンク
     */
    static $DOMAIN_SOFTBANK = array(
        'softbank.ne.jp',
        // ボーダフォン
        'd.vodafone.ne.jp',
        'h.vodafone.ne.jp',
        't.vodafone.ne.jp',
        'c.vodafone.ne.jp',
        'r.vodafone.ne.jp',
        'k.vodafone.ne.jp',
        'n.vodafone.ne.jp',
        's.vodafone.ne.jp',
        'p.vodafone.ne.jp',
    );
    /**
     * @var array : ウィルコム
     */
    static $DOMAIN_WILLCOM = array(
        'pdx.ne.jp',
        'yy.pdx.ne.jp'
    );

    /**
     * @param $career
     * @return array|null
     */
    static public function getDomain($career){
        $arr = self::getDomainAll();
        if(isset($arr[$career])){
            return $arr[$career];
        }
        return null;
    }

    /**
     * @return array
     */
    static public function getDomainAll(){
        $arr = array();
        $arr[SYSTEM_CARRIER_DOCOMO] = self::$DOMAIN_DOCOMO;
        $arr[SYSTEM_CARRIER_AU] = self::$DOMAIN_AU;
        $arr[SYSTEM_CARRIER_SOFTBANK] = self::$DOMAIN_SOFTBANK;
        $arr[SYSTEM_CARRIER_WILLCOM] = self::$DOMAIN_WILLCOM;
        return $arr;
    }

}

/**
 * Class class_define
 */
class class_define{
    /**
     * @var array : 都道府県
     */
    static $PREF = array(
        '1' => '北海道',
        '2' => '青森県',
        '3' => '岩手県',
        '4' => '宮城県',
        '5' => '秋田県',
        '6' => '山形県',
        '7' => '福島県',
        '8' => '茨城県',
        '9' => '栃木県',
        '10' => '群馬県',
        '11' => '埼玉県',
        '12' => '千葉県',
        '13' => '東京都',
        '14' => '神奈川県',
        '15' => '新潟県',
        '16' => '富山県',
        '17' => '石川県',
        '18' => '福井県',
        '19' => '山梨県',
        '20' => '長野県',
        '21' => '岐阜県',
        '22' => '静岡県',
        '23' => '愛知県',
        '24' => '三重県',
        '25' => '滋賀県',
        '26' => '京都府',
        '27' => '大阪府',
        '28' => '兵庫県',
        '29' => '奈良県',
        '30' => '和歌山県',
        '31' => '鳥取県',
        '32' => '島根県',
        '33' => '岡山県',
        '34' => '広島県',
        '35' => '山口県',
        '36' => '徳島県',
        '37' => '香川県',
        '38' => '愛媛県',
        '39' => '高知県',
        '40' => '福岡県',
        '41' => '佐賀県',
        '42' => '長崎県',
        '43' => '熊本県',
        '44' => '大分県',
        '45' => '宮崎県',
        '46' => '鹿児島県',
        '47' => '沖縄県'
    );

    /**
     * @var array : 都道府県
     */
    static $PREF_EN = array(
        '1' => 'hokkaido',
        '2' => 'aomori',
        '3' => 'iwate',
        '4' => 'miyagi',
        '5' => 'akita',
        '6' => 'yamagata',
        '7' => 'fukushima',
        '8' => 'ibaragi',
        '9' => 'totigi',
        '10' => 'gunma',
        '11' => 'saitama',
        '12' => 'chiba',
        '13' => 'tokyo',
        '14' => 'kanagawa',
        '15' => 'nigata',
        '16' => 'toyama',
        '17' => 'ishikawa',
        '18' => 'fukui',
        '19' => 'yamanashi',
        '20' => 'nagano',
        '21' => 'gifu',
        '22' => 'shizuoka',
        '23' => 'aichi',
        '24' => 'mie',
        '25' => 'shiga',
        '26' => 'kyoto',
        '27' => 'osaka',
        '28' => 'hyogo',
        '29' => 'nara',
        '30' => 'wakayama',
        '31' => 'tottori',
        '32' => 'shimane',
        '33' => 'okayama',
        '34' => 'hiroshima',
        '35' => 'yamaguchi',
        '36' => 'tokushima',
        '37' => 'kagawa',
        '38' => 'ehime',
        '39' => 'kochi',
        '40' => 'fukuoka',
        '41' => 'shiga',
        '42' => 'nagasaki',
        '43' => 'kumamoto',
        '44' => 'oita',
        '45' => 'miyazaki',
        '46' => 'kagoshima',
        '47' => 'okinawa'
    );

    /**
     * @var array : 性別
     */
    static $SEX = array(
        1 => '男',
        2 => '女',
    );

    /**
     * @var array : 曜日
     */
    static $WEEK = array(
        0 => 'sun',
        1 => 'mon',
        2 => 'tue',
        3 => 'wed',
        4 => 'thu',
        5 => 'fri',
        6 => 'sta',
    );
    static $WEEK_JP = array(
        0 => '日',
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土'
    );
    /**
     * @var array : 星座
     */
    static $CONSTELLATIONS = array(
        1 => 'おひつじ座', // 3/21-4/19 Aries (Ram)
        2 => 'おうし座', // 3/21-4/19 Taurus (Bull)
        3 => 'ふたご座', // 5/21-6/21 Gemini (Twins)
        4 => 'かに座', // 6/22-7/22 Cancer (Crab)
        5 => 'しし座', // 7/23-8/22 Leo (Lion)
        6 => 'おとめ座', // 8/23-9/22 Virgo (Virgin)
        7 => 'てんびん座', // 9/23-10/23 Libra (Scales)
        8 => 'さそり座', // 10/24-11/21 Scorpius (Scorpion)
        9 => 'いて座', // 11/22-12/21 Sagittarius (Archer)
        10 => 'やぎ座', // 12/22-1/19 Capricornus (Goat)
        11 => 'みずがめ座', // 1/20-2/18 Aquarius (Water Bearer)
        12 => 'うお座', // 2/19-3/20 Pisces (Fishes)
    );

    /**
     * @var array : 血液型
     */
    static $BLOOD = array(
        'A' => 'A型',
        'B' => 'B型',
        'O' => 'O型',
        'AB' => 'AB型'
    );

    /**
     * @var array : 和暦
     */
    static $WAREKI = array(
        '247' => array('name' => '明治', 'start' => '1868/09/08'),
        '248' => array('name' => '大正', 'start' => '1912/07/30'),
        '249' => array('name' => '昭和', 'start' => '1926/12/25'),
        '250' => array('name' => '平成', 'start' => '1989/01/08'),
    );

    /**
     * @var array : 国(IOCコード変換)
     */
    static $COUNTRY_IOC = array(
        'AFG' => '004',
        'UAE' => '784',
        'YEM' => '887',
        'ISR' => '376',
        'IRQ' => '368',
        'IRI' => '364',
        'IND' => '356',
        'OMA' => '512',
        'QAT' => '634',
        'CAM' => '116',
        'KUW' => '414',
        'KSA' => '682',
        'SYR' => '760',
        'SIN' => '702',
        'SRI' => '144',
        'KOR' => '410',
        'THA' => '764',
        'TUR' => '792',
        'JPN' => '392',
        'NEP' => '524',
        'BRN' => '048',
        'PAK' => '586',
        'BAN' => '050',
        'PHI' => '608',
        'BHU' => '064',
        'BRU' => '096',
        'VIE' => '704',
        'HKG' => '344',
        'MAC' => '446',
        'MAS' => '458',
        'MYA' => '104',
        'MDV' => '462',
        'MGL' => '496',
        'JOR' => '400',
        'LAO' => '418',
        'LIB' => '422',
        'PLE' => '',
        'TPE' => '158',
        'CHN' => '156',
        'PRK' => '408',
        'IOA' => '626',
        'AZE' => '031',
        'ARM' => '051',
        'UKR' => '804',
        'UZB' => '860',
        'KAZ' => '398',
        'KGZ' => '417',
        'GEO' => '268',
        'TJK' => '762',
        'TKM' => '795',
        'BLR' => '112',
        'MDA' => '498',
        'RUS' => '643',
        'ISL' => '352',
        'IRL' => '372',
        'ALB' => '008',
        'AND' => '020',
        'ITA' => '380',
        'EST' => '233',
        'AUT' => '040',
        'NED' => '528',
        'CYP' => '196',
        'GRE' => '300',
        'GBR' => '826',
        'ENG' => '',
        'WAL' => '',
        'SCO' => '',
        'NIR' => '',
        'IOM' => '',
        'CRO' => '191',
        'SMR' => '674',
        'SUI' => '756',
        'SWE' => '752',
        'ESP' => '724',
        'SVK' => '703',
        'SLO' => '705',
        'SCG' => '891',
        'URS' => '810',
        'RCS' => '200',
        'CZE' => '203',
        'DEN' => '208',
        'DDR' => '278',
        'GER' => '276',
        'NOR' => '578',
        'HUN' => '348',
        'FIN' => '246',
        'FRO' => '234',
        'FRA' => '250',
        'FXX' => '249',
        'BUL' => '100',
        'BEL' => '056',
        'POL' => '616',
        'BIH' => '070',
        'POR' => '620',
        'MKD' => '807',
        'MLT' => '470',
        'MON' => '492',
        'YUG' => '891',
        'LAT' => '428',
        'LTU' => '440',
        'LIE' => '438',
        'ROM' => '642',
        'LUX' => '442',
        'ALG' => '012',
        'ANG' => '024',
        'UGA' => '800',
        'EGY' => '818',
        'ETH' => '231',
        'ERI' => '232',
        'GHA' => '288',
        'CPV' => '132',
        'GAB' => '266',
        'CMR' => '120',
        'GAM' => '270',
        'GBS' => '624',
        'GUI' => '324',
        'KEN' => '404',
        'CIV' => '384',
        'COM' => '174',
        'CGO' => '178',
        'COD' => '180',
        'ZAR' => '180',
        'STP' => '678',
        'ZAM' => '894',
        'SLE' => '694',
        'DJI' => '262',
        'ZIM' => '716',
        'SUD' => '736',
        'SWZ' => '748',
        'SEY' => '690',
        'SEN' => '686',
        'SOM' => '706',
        'TAN' => '834',
        'CHA' => '148',
        'TUN' => '788',
        'TOG' => '768',
        'NGR' => '566',
        'NAM' => '516',
        'NIG' => '562',
        'BUR' => '854',
        'BDI' => '108',
        'BEN' => '204',
        'BOT' => '072',
        'MAD' => '450',
        'MAW' => '454',
        'MLI' => '466',
        'MRI' => '480',
        'MTN' => '478',
        'MOZ' => '508',
        'MAR' => '504',
        'LBR' => '430',
        'RWA' => '646',
        'LES' => '426',
        'LBA' => '434',
        'GEQ' => '226',
        'CAF' => '140',
        'RSA' => '710',
        'USA' => '840',
        'CAN' => '124',
        'ARU' => '533',
        'ANT' => '028',
        'ESA' => '222',
        'AHO' => '530',
        'CUB' => '192',
        'GUA' => '320',
        'GRN' => '308',
        'CAY' => '136',
        'CRC' => '188',
        'JAM' => '388',
        'SKN' => '659',
        'VIN' => '670',
        'LCA' => '662',
        'DOM' => '214',
        'DMA' => '212',
        'TRI' => '780',
        'NCA' => '558',
        'BER' => '060',
        'HAI' => '332',
        'PAN' => '591',
        'BAH' => '044',
        'BAR' => '052',
        'PUR' => '630',
        'ISV' => '850',
        'BIZ' => '084',
        'HON' => '340',
        'MEX' => '484',
        'MSR' => '500',
        'IVB' => '092',
        'ARG' => '032',
        'URU' => '858',
        'ECU' => '218',
        'GUY' => '328',
        'COL' => '170',
        'SUR' => '740',
        'CHI' => '152',
        'PAR' => '600',
        'BRA' => '076',
        'VEN' => '862',
        'PER' => '604',
        'BOL' => '068',
        'AUS' => '036',
        'GUM' => '316',
        'COK' => '184',
        'SAM' => '882',
        'SOL' => '090',
        'TGA' => '776',
        'NRU' => '520',
        'NZL' => '554',
        'VAN' => '548',
        'PNG' => '598',
        'PLW' => '585',
        'FIJ' => '242',
        'FSM' => '583',
    );
    /**
     * @var array : 国
     */
    static $COUNTRY = array(
        "020" => "アンドラ公国",
        "784" => "アラブ首長国連邦",
        "004" => "アフガニスタン・イスラム国",
        "028" => "アンチグア・バーブーダ",
        "660" => "アンギラ",
        "008" => "アルバニア共和国",
        "051" => "アルメニア共和国",
        "530" => "オランダ領アンチル",
        "024" => "アンゴラ共和国",
        "010" => "南極",
        "032" => "アルゼンチン共和国",
        "016" => "米領サモア",
        "040" => "オーストリア共和国",
        "036" => "オーストラリア",
        "533" => "アルバ",
        "031" => "アゼルバイジャン共和国",
        "070" => "ボスニア・ヘルツェゴビナ共和国",
        "052" => "バルバドス",
        "050" => "バングラデシュ人民共和国",
        "056" => "ベルギー王国",
        "854" => "ブルキナファソ",
        "100" => "ブルガリア共和国",
        "048" => "バーレーン国",
        "108" => "ブルンジ共和国",
        "204" => "ベナン共和国",
        "060" => "バーミューダ諸島",
        "096" => "ブルネイ・ダルサラーム国",
        "068" => "ボリビア共和国",
        "076" => "ブラジル連邦共和国",
        "044" => "バハマ国",
        "064" => "ブータン王国",
        "074" => "ブーベ島",
        "072" => "ボツワナ共和国",
        "112" => "ベラルーシ共和国",
        "084" => "ベリーズ",
        "124" => "カナダ",
        "166" => "ココス諸島",
        "140" => "中央アフリカ共和国",
        "178" => "コンゴ共和国",
        "756" => "スイス連邦",
        "384" => "コートジボアール共和国",
        "184" => "クック諸島",
        "152" => "チリ共和国",
        "120" => "カメルーン共和国",
        "156" => "中華人民共和国",
        "170" => "コロンビア共和国",
        "188" => "コスタリカ共和国",
        "192" => "キューバ共和国",
        "132" => "カーボベルデ共和国",
        "162" => "クリスマス島",
        "196" => "キプロス共和国",
        "203" => "チェコ共和国",
        "276" => "ドイツ連邦共和国",
        "262" => "ジブチ共和国",
        "208" => "デンマーク王国",
        "212" => "ドミニカ国",
        "214" => "ドミニカ共和国",
        "012" => "アルジェリア民主人民共和国",
        "218" => "エクアドル共和国",
        "233" => "エストニア共和国",
        "818" => "エジプト・アラブ共和国",
        "732" => "西サハラ",
        "232" => "エリトリア",
        "724" => "スペイン",
        "231" => "エチオピア",
        "246" => "フィンランド共和国",
        "242" => "フィジー共和国",
        "238" => "フォークランド(マルビナス)諸島",
        "583" => "ミクロネシア連邦",
        "234" => "フェロー諸島",
        "250" => "フランス共和国",
        "249" => "フランス本国",
        "266" => "ガボン共和国",
        "826" => "イギリス",
        "308" => "グレナダ",
        "268" => "グルジア共和国",
        "254" => "仏領ギアナ",
        "288" => "ガーナ共和国",
        "292" => "ジブラルタル",
        "304" => "グリーンランド",
        "270" => "ガンビア共和国",
        "324" => "ギニア共和国",
        "312" => "グアドループ島",
        "226" => "赤道ギニア共和国",
        "300" => "ギリシア共和国",
        "239" => "南ジョージア島・南サンドイッチ諸島",
        "320" => "グアテマラ共和国",
        "316" => "グアム",
        "624" => "ギニアビサオ共和国",
        "328" => "ガイアナ協同共和国",
        "344" => "ホンコン (香港)",
        "334" => "ヘアド島・マクドナルド諸島",
        "340" => "ホンジュラス共和国",
        "191" => "クロアチア共和国",
        "332" => "ハイチ共和国",
        "348" => "ハンガリー共和国",
        "360" => "インドネシア共和国",
        "372" => "アイルランド",
        "376" => "イスラエル国",
        "356" => "インド",
        "086" => "英領インド洋地域",
        "368" => "イラク共和国",
        "364" => "イラン・イスラム共和国",
        "352" => "アイスランド共和国",
        "380" => "イタリア共和国",
        "388" => "ジャマイカ",
        "400" => "ヨルダン・ハシミテ王国",
        "392" => "日本",
        "404" => "ケニア共和国",
        "417" => "キルギスタン共和国",
        "116" => "カンボディア王国",
        "296" => "キリバス共和国",
        "174" => "コモロ・イスラム連邦共和国",
        "659" => "セントクリストファー・ネイビス",
        "408" => "北朝鮮",
        "410" => "大韓民国",
        "414" => "クウェート国",
        "136" => "ケイマン諸島",
        "398" => "カザフスタン共和国",
        "418" => "ラオス人民民主共和国",
        "422" => "レバノン共和国",
        "662" => "セントルシア",
        "438" => "リヒテンシュタイン公国",
        "144" => "スリランカ民主社会主義共和国",
        "430" => "リベリア共和国",
        "426" => "レソト王国",
        "440" => "リトアニア共和国",
        "442" => "ルクセンブルク大公国",
        "428" => "ラトビア共和国",
        "434" => "社会主義人民リビア・アラブ国",
        "504" => "モロッコ王国",
        "492" => "モナコ公国",
        "498" => "モルドバ共和国",
        "450" => "マダガスカル共和国",
        "584" => "マーシャル諸島共和国",
        "466" => "マリ共和国",
        "104" => "ミャンマー連邦",
        "496" => "モンゴル国",
        "446" => "マカオ(澳門)",
        "580" => "北マリアナ諸島",
        "474" => "マルチニーク島",
        "478" => "モーリタニア・イスラム共和国",
        "500" => "モントセラト",
        "470" => "マルタ共和国",
        "480" => "モーリシャス共和国",
        "462" => "モルジブ共和国",
        "454" => "マラウイ共和国",
        "484" => "メキシコ合衆国",
        "458" => "マレーシア",
        "508" => "モザンビーク共和国",
        "516" => "ナミビア共和国",
        "540" => "ニューカレドニア",
        "562" => "ニジェール共和国",
        "574" => "ノーフォーク島",
        "566" => "ナイジェリア連邦共和国",
        "558" => "ニカラグア共和国",
        "528" => "オランダ王国",
        "578" => "ノルウェー王国",
        "524" => "ネパール王国",
        "520" => "ナウル共和国",
        "570" => "ニウエ",
        "554" => "ニュージーランド",
        "512" => "オマーン国",
        "591" => "パナマ共和国",
        "604" => "ペルー共和国",
        "258" => "仏領ポリネシア",
        "598" => "パプアニューギニア",
        "608" => "フィリピン共和国",
        "586" => "パキスタン・イスラム共和国",
        "616" => "ポーランド共和国",
        "666" => "サンピエール島・ミクロン島",
        "612" => "ピトケアン島",
        "630" => "プエルトリコ",
        "620" => "ポルトガル共和国",
        "585" => "パラオ",
        "600" => "パラグアイ共和国",
        "634" => "カタール国",
        "638" => "レユニオン",
        "642" => "ルーマニア",
        "643" => "ロシア連邦",
        "646" => "ルワンダ共和国",
        "682" => "サウジアラビア王国",
        "090" => "ソロモン諸島",
        "690" => "セイシェル共和国",
        "736" => "スーダン共和国",
        "752" => "スウェーデン王国",
        "702" => "シンガポール共和国",
        "654" => "セントヘレナ島",
        "705" => "スロベニア共和国",
        "744" => "スバールバル諸島・ヤンマイエン島",
        "703" => "スロバキア共和国",
        "694" => "シエラレオネ共和国",
        "674" => "サンマリノ共和国",
        "686" => "セネガル共和国",
        "706" => "ソマリア民主共和国",
        "740" => "スリナム共和国",
        "678" => "サントメ・プリンシペ民主共和国",
        "222" => "エルサルバドル共和国",
        "760" => "シリア・アラブ共和国",
        "748" => "スワジランド王国",
        "796" => "タークス諸島・カイコス諸島",
        "148" => "チャド共和国",
        "260" => "仏領極南諸島",
        "768" => "トーゴ共和国",
        "764" => "タイ王国",
        "762" => "タジキスタン共和国",
        "772" => "トケラウ諸島",
        "795" => "トルクメニスタン",
        "788" => "チュニジア共和国",
        "776" => "トンガ王国",
        "626" => "東チモール",
        "792" => "トルコ共和国",
        "780" => "トリニダード・トバゴ共和国",
        "798" => "ツバル",
        "158" => "台湾",
        "834" => "タンザニア連合共和国",
        "804" => "ウクライナ",
        "800" => "ウガンダ共和国",
        "581" => "米領太平洋諸島",
        "840" => "アメリカ",
        "858" => "ウルグアイ東方共和国",
        "860" => "ウズベキスタン共和国",
        "336" => "バチカン市国",
        "670" => "セントビンセント及びグレナディーン諸島",
        "862" => "ベネズエラ共和国",
        "092" => "英領バージン諸島",
        "850" => "米領バージン諸島",
        "704" => "ベトナム社会主義共和国",
        "548" => "バヌアツ共和国",
        "876" => "ワリス・フテュナ諸島",
        "882" => "西サモア",
        "887" => "イエメン共和国",
        "175" => "マイヨット島",
        "891" => "ユーゴスラビア連邦共和国",
        "710" => "南アフリカ共和国",
        "894" => "ザンビア共和国",
        "180" => "ザイール共和国",
        "716" => "ジンバブエ共和国",
    );

}