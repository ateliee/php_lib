<?php
//============================================
// class_mobile.php
//============================================
include_once( SYSTEM_PATH_FUNC.'func_agent.php' );

// 機種別絵文字一覧(DOCOMO)
$L_MOBILE_EMOJI_DOCOMO = array(
      '1' => array( '63647','E63E' ),
      '2' => array( '63648','E63F' ),
      '3' => array( '63649','E640' ),
      '4' => array( '63650','E641' ),
      '5' => array( '63651','E642' ),
      '6' => array( '63652','E643' ),
      '7' => array( '63653','E644' ),
      '8' => array( '63654','E645' ),
      '9' => array( '63655','E646' ),
      '10' => array( '63656','E647' ),
      '11' => array( '63657','E648' ),
      '12' => array( '63658','E649' ),
      '13' => array( '63659','E64A' ),
      '14' => array( '63660','E64B' ),
      '15' => array( '63661','E64C' ),
      '16' => array( '63662','E64D' ),
      '17' => array( '63663','E64E' ),
      '18' => array( '63664','E64F' ),
      '19' => array( '63665','E650' ),
      '20' => array( '63666','E651' ),
      '21' => array( '63667','E652' ),
      '22' => array( '63668','E653' ),
      '23' => array( '63669','E654' ),
      '24' => array( '63670','E655' ),
      '25' => array( '63671','E656' ),
      '26' => array( '63672','E657' ),
      '27' => array( '63673','E658' ),
      '28' => array( '63674','E659' ),
      '29' => array( '63675','E65A' ),
      '30' => array( '63676','E65B' ),
      '31' => array( '63677','E65C' ),
      '32' => array( '63678','E65D' ),
      '33' => array( '63679','E65E' ),
      '34' => array( '63680','E65F' ),
      '35' => array( '63681','E660' ),
      '36' => array( '63682','E661' ),
      '37' => array( '63683','E662' ),
      '38' => array( '63684','E663' ),
      '39' => array( '63685','E664' ),
      '40' => array( '63686','E665' ),
      '41' => array( '63687','E666' ),
      '42' => array( '63688','E667' ),
      '43' => array( '63689','E668' ),
      '44' => array( '63690','E669' ),
      '45' => array( '63691','E66A' ),
      '46' => array( '63692','E66B' ),
      '47' => array( '63693','E66C' ),
      '48' => array( '63694','E66D' ),
      '49' => array( '63695','E66E' ),
      '50' => array( '63696','E66F' ),
      '51' => array( '63697','E670' ),
      '52' => array( '63698','E671' ),
      '53' => array( '63699','E672' ),
      '54' => array( '63700','E673' ),
      '55' => array( '63701','E674' ),
      '56' => array( '63702','E675' ),
      '57' => array( '63703','E676' ),
      '58' => array( '63704','E677' ),
      '59' => array( '63705','E678' ),
      '60' => array( '63706','E679' ),
      '61' => array( '63707','E67A' ),
      '62' => array( '63708','E67B' ),
      '63' => array( '63709','E67C' ),
      '64' => array( '63710','E67D' ),
      '65' => array( '63711','E67E' ),
      '66' => array( '63712','E67F' ),
      '67' => array( '63713','E680' ),
      '68' => array( '63714','E681' ),
      '69' => array( '63715','E682' ),
      '70' => array( '63716','E683' ),
      '71' => array( '63717','E684' ),
      '72' => array( '63718','E685' ),
      '73' => array( '63719','E686' ),
      '74' => array( '63720','E687' ),
      '75' => array( '63721','E688' ),
      '76' => array( '63722','E689' ),
      '77' => array( '63723','E68A' ),
      '78' => array( '63724','E68B' ),
      '79' => array( '63725','E68C' ),
      '80' => array( '63726','E68D' ),
      '81' => array( '63727','E68E' ),
      '82' => array( '63728','E68F' ),
      '83' => array( '63729','E690' ),
      '84' => array( '63730','E691' ),
      '85' => array( '63731','E692' ),
      '86' => array( '63732','E693' ),
      '87' => array( '63733','E694' ),
      '88' => array( '63734','E695' ),
      '89' => array( '63735','E696' ),
      '90' => array( '63736','E697' ),
      '91' => array( '63737','E698' ),
      '92' => array( '63738','E699' ),
      '93' => array( '63739','E69A' ),
      '94' => array( '63740','E69B' ),
      '95' => array( '63808','E69C' ),
      '96' => array( '63809','E69D' ),
      '97' => array( '63810','E69E' ),
      '98' => array( '63811','E69F' ),
      '99' => array( '63812','E6A0' ),
      '100' => array( '63813','E6A1' ),
      '101' => array( '63814','E6A2' ),
      '102' => array( '63815','E6A3' ),
      '103' => array( '63816','E6A4' ),
      '104' => array( '63817','E6A5' ),
      '105' => array( '63858','E6CE' ),
      '106' => array( '63859','E6CF' ),
      '107' => array( '63860','E6D0' ),
      '108' => array( '63861','E6D1' ),
      '109' => array( '63862','E6D2' ),
      '110' => array( '63796','E6D3' ),      // メール
      '111' => array( '63864','E6D4' ),
      '112' => array( '63865','E6D5' ),
      '113' => array( '63866','E6D6' ),
      '114' => array( '63867','E6D7' ),
      '115' => array( '63868','E6D8' ),
      '116' => array( '63869','E6D9' ),
      '117' => array( '63870','E6DA' ),
      '118' => array( '63872','E6DB' ),
      '119' => array( '63805','E6DC' ),      // サーチ（調べる）
      '120' => array( '63874','E6DD' ),
      '121' => array( '63875','E6DE' ),
      '122' => array( '63876','E6DF' ),
      '123' => array( '63877','E6E0' ),
      '124' => array( '63878','E6E1' ),
      '125' => array( '63879','E6E2' ),
      '126' => array( '63880','E6E3' ),
      '127' => array( '63881','E6E4' ),
      '128' => array( '63882','E6E5' ),
      '129' => array( '63883','E6E6' ),
      '130' => array( '63884','E6E7' ),
      '131' => array( '63885','E6E8' ),
      '132' => array( '63886','E6E9' ),
      '133' => array( '63887','E6EA' ),
      '134' => array( '63888','E6EB' ),
      '135' => array( '63920','E70B' ),
      '136' => array( '63889','E6EC' ),
      '137' => array( '63890','E6ED' ),
      '138' => array( '63891','E6EE' ),
      '139' => array( '63892','E6EF' ),
      '140' => array( '63893','E6F0' ),
      '141' => array( '63894','E6F1' ),
      '142' => array( '63895','E6F2' ),
      '143' => array( '63896','E6F3' ),
      '144' => array( '63897','E6F4' ),
      '145' => array( '63898','E6F5' ),
      '146' => array( '63899','E6F6' ),
      '147' => array( '63900','E6F7' ),
      '148' => array( '63901','E6F8' ),
      '149' => array( '63902','E6F9' ),
      '150' => array( '63903','E6FA' ),
      '151' => array( '63904','E6FB' ),
      '152' => array( '63905','E6FC' ),
      '153' => array( '63906','E6FD' ),
      '154' => array( '63907','E6FE' ),
      '155' => array( '63908','E6FF' ),
      '156' => array( '63841','E700' ),      // バッド（下向き矢印）
      '157' => array( '63910','E701' ),
      '158' => array( '63911','E702' ),
      '159' => array( '63912','E703' ),
      '160' => array( '63913','E704' ),
      '161' => array( '63914','E705' ),
      '162' => array( '63915','E706' ),
      '163' => array( '63916','E707' ),
      '164' => array( '63917','E708' ),
      '165' => array( '63918','E709' ),
      '166' => array( '63919','E70A' ),
      '167' => array( '63824','E6AC' ),
      '168' => array( '63825','E6AD' ),
      '169' => array( '63826','E6AE' ),
      '170' => array( '63829','E6B1' ),
      '171' => array( '63830','E6B2' ),
      '172' => array( '63831','E6B3' ),
      '173' => array( '63835','E6B7' ),
      '174' => array( '63836','E6B8' ),
      '175' => array( '63837','E6B9' ),
      '176' => array( '63838','E6BA' ),
      '177' => array( '63921','E70C' ),
      '178' => array( '63922','E70D' ),
      '179' => array( '63923','E70E' ),
      '180' => array( '63924','E70F' ),
      '181' => array( '63925','E710' ),
      '182' => array( '63926','E711' ),
      '183' => array( '63927','E712' ),
      '184' => array( '63928','E713' ),
      '185' => array( '63929','E714' ),
      '186' => array( '63930','E715' ),
      '187' => array( '63931','E716' ),
      '188' => array( '63932','E717' ),
      '189' => array( '63933','E718' ),
      '190' => array( '63934','E719' ),
      '191' => array( '63935','E71A' ),
      '192' => array( '63936','E71B' ),
      '193' => array( '63937','E71C' ),
      '194' => array( '63938','E71D' ),
      '195' => array( '63939','E71E' ),
      '196' => array( '63940','E71F' ),
      '197' => array( '63941','E720' ),
      '198' => array( '63942','E721' ),
      '199' => array( '63943','E722' ),
      '200' => array( '63944','E723' ),
      '201' => array( '63945','E724' ),
      '202' => array( '63946','E725' ),
      '203' => array( '63947','E726' ),
      '204' => array( '63948','E727' ),
      '205' => array( '63949','E728' ),
      '206' => array( '63950','E729' ),
      '207' => array( '63951','E72A' ),
      '208' => array( '63952','E72B' ),
      '209' => array( '63953','E72C' ),
      '210' => array( '63954','E72D' ),
      '211' => array( '63955','E72E' ),
      '212' => array( '63956','E72F' ),
      '213' => array( '63957','E730' ),
      '214' => array( '63958','E731' ),
      '215' => array( '63959','E732' ),
      '216' => array( '63960','E733' ),
      '217' => array( '63961','E734' ),
      '218' => array( '63962','E735' ),
      '219' => array( '63963','E736' ),
      '220' => array( '63964','E737' ),
      '221' => array( '63965','E738' ),
      '222' => array( '63966','E739' ),
      '223' => array( '63967','E73A' ),
      '224' => array( '63968','E73B' ),
      '225' => array( '63969','E73C' ),
      '226' => array( '63970','E73D' ),
      '227' => array( '63971','E73E' ),
      '228' => array( '63972','E73F' ),
      '229' => array( '63973','E740' ),
      '230' => array( '63974','E741' ),
      '231' => array( '63975','E742' ),
      '232' => array( '63976','E743' ),
      '233' => array( '63977','E744' ),
      '234' => array( '63978','E745' ),
      '235' => array( '63979','E746' ),
      '236' => array( '63980','E747' ),
      '237' => array( '63981','E748' ),
      '238' => array( '63982','E749' ),
      '239' => array( '63983','E74A' ),
      '240' => array( '63984','E74B' ),
      '241' => array( '63985','E74C' ),
      '242' => array( '63986','E74D' ),
      '243' => array( '63987','E74E' ),
      '244' => array( '63988','E74F' ),
      '245' => array( '63989','E750' ),
      '246' => array( '63990','E751' ),
      '247' => array( '63991','E752' ),
      '248' => array( '63992','E753' ),
      '249' => array( '63993','E754' ),
      '250' => array( '63994','E755' ),
      '251' => array( '63995','E756' ),
      '252' => array( '63996','E757' ),
      '253' => array( '63716','E683' )
);

// 機種別絵文字一覧(AU)
$L_MOBILE_EMOJI_AU = array(
      '1' => array( '','E488' ),
      '2' => array( '','E48D' ),
      '3' => array( '','E48C' ),
      '4' => array( '','E485' ),
      '5' => array( '','E487' ),
      '6' => array( '','E469' ),
      '7' => array( '','E598' ),
      '8' => array( '','EAE8' ),
      '9' => array( '','E48F' ),
      '10' => array( '','E490' ),
      '11' => array( '','E491' ),
      '12' => array( '','E492' ),
      '13' => array( '','E493' ),
      '14' => array( '','E494' ),
      '15' => array( '','E495' ),
      '16' => array( '','E496' ),
      '17' => array( '','E497' ),
      '18' => array( '','E498' ),
      '19' => array( '','E499' ),
      '20' => array( '','E49A' ),
      '21' => array( '','E46B' ),
      '22' => array( '','E4BA' ),
      '23' => array( '','E599' ),
      '24' => array( '','E4B7' ),
      '25' => array( '','E4B6' ),
      '26' => array( '','EAAC' ),
      '27' => array( '','E59A' ),
      '28' => array( '','E4B9' ),
      '29' => array( '','E59B' ),
      '30' => array( '','E4B5' ),
      '31' => array( '','E5BC' ),
      '32' => array( '','E4B0' ),
      '33' => array( '','E4B1' ),
      '34' => array( '','E4B1' ),
      '35' => array( '','E4AF' ),
      '36' => array( '','EA82' ),
      '37' => array( '','E4B3' ),
      '38' => array( '','E4AB' ),
      '39' => array( '','E4AD' ),
      '40' => array( '','E5DE' ),
      '41' => array( '','E5DF' ),
      '42' => array( '','E4AA' ),
      '43' => array( '','E4A3' ),
      '44' => array( '','EA81' ),
      '45' => array( '','E4A4' ),
      '46' => array( '','E571' ),
      '47' => array( '','E4A6' ),
      '48' => array( '','E46A' ),
      '49' => array( '','E4A5' ),
      '50' => array( '','E4AC' ),
      '51' => array( '','E597' ),
      '52' => array( '','E4C2' ),
      '53' => array( '','E4C3' ),
      '54' => array( '','E4D6' ),
      '55' => array( '','E51A' ),
      '56' => array( '','E516' ),
      '57' => array( '','E503' ),
      '58' => array( '','E517' ),
      '59' => array( '','E555' ),
      '60' => array( '','E46D' ),
      '61' => array( '','E508' ),
      '62' => array( '','E59C' ),
      '63' => array( '','EAF5' ),
      '64' => array( '','E59E' ),
      '65' => array( '','E49E' ),
      '66' => array( '','E47D' ),
      '67' => array( '','E47E' ),
      '68' => array( '','E515' ),
      '69' => array( '','E49C' ),
      '70' => array( '','E49F' ),
      '71' => array( '','E59F' ),
      '72' => array( '','E4CF' ),
      '73' => array( '','E5A0' ),
      '74' => array( '','E596' ),
      '75' => array( '','E588' ),
      '76' => array( '','EA92' ),
      '77' => array( '','E502' ),
      '78' => array( '','E4C6' ),
      '79' => array( '','E50C' ),
      '80' => array( '','EAA5' ),
      '81' => array( '','E5A1' ),
      '82' => array( '','E5A2' ),
      '83' => array( '','E5A3' ),
      '84' => array( '','E5A4' ),
      '85' => array( '','E5A5' ),
      '86' => array( '','EB83' ),
      '87' => array( '','E5A6' ),
      '88' => array( '','E5A7' ),
      '89' => array( '','E54D' ),
      '90' => array( '','E54C' ),
      '91' => array( '','EB2A' ),
      '92' => array( '','EB2B' ),
      '93' => array( '','E4FE' ),
      '94' => array( '','E47F' ),
      '95' => array( '','E5A8' ),
      '96' => array( '','E5A9' ),
      '97' => array( '','E5AA' ),
      '98' => array( '','E486' ),
      '99' => array( '','E489' ),
      '100' => array( '','E4E1' ),
      '101' => array( '','E4DB' ),
      '102' => array( '','E4B4' ),
      '103' => array( '','E4C9' ),
      '104' => array( '','E556' ),
      '105' => array( '','EB08' ),
      '106' => array( '','EB62' ),
      '107' => array( '','E520' ),
      '108' => array( '','E577' ),
      '109' => array( '','E577' ),
      '110' => array( '','E521' ),
      '111' => array( '','E54E' ),
      '112' => array( '','E54E' ),
      '113' => array( '','E57D' ),
      '114' => array( '','E578' ),
      '115' => array( '','EA88' ),
      '116' => array( '','E519' ),
      '117' => array( '','E55D' ),
      '118' => array( '','E5AB' ),
      '119' => array( '','E518' ),
      '120' => array( '','E5B5' ),
      '121' => array( '','EB2C' ),
      '122' => array( '','E596' ),
      '123' => array( '','EB84' ),
      '124' => array( '','E52C' ),
      '125' => array( '','E522' ),
      '126' => array( '','E523' ),
      '127' => array( '','E524' ),
      '128' => array( '','E525' ),
      '129' => array( '','E526' ),
      '130' => array( '','E527' ),
      '131' => array( '','E528' ),
      '132' => array( '','E529' ),
      '133' => array( '','E52A' ),
      '134' => array( '','E5AC' ),
      '135' => array( '','E5AD' ),
      '136' => array( '','E595' ),
      '137' => array( '','EB75' ),
      '138' => array( '','E477' ),
      '139' => array( '','E478' ),
      '140' => array( '','E471' ),
      '141' => array( '','E472' ),
      '142' => array( '','EAC0' ),
      '143' => array( '','EAC3' ),
      '144' => array( '','E5AE' ),
      '145' => array( '','EB2D' ),
      '146' => array( '','E5BE' ),
      '147' => array( '','E4BC' ),
      '148' => array( '','E536' ),
      '149' => array( '','E4EB' ),
      '150' => array( '','EAAB' ),
      '151' => array( '','E476' ),
      '152' => array( '','E4E5' ),
      '153' => array( '','E4F3' ),
      '154' => array( '','E47A' ),
      '155' => array( '','E505' ),
      '156' => array( '','EB2E' ),
      '157' => array( '','E475' ),
      '158' => array( '','E482' ),
      '159' => array( '','EB2F' ),
      '160' => array( '','EB30' ),
      '161' => array( '','E5B0' ),
      '162' => array( '','E5B1' ),
      '163' => array( '','E4E6' ),
      '164' => array( '','E4F4' ),
      '165' => array( '','EB7C' ),
      '166' => array( '','EB31' ),
      '167' => array( '','E4BE' ),
      '168' => array( '','E4C7' ),
      '169' => array( '','EB03' ),
      '170' => array( '','E4FC' ),
      '171' => array( '','EB1C' ),
      '172' => array( '','EAF1' ),
      '173' => array( '','E552' ),
      '174' => array( '','EB7A' ),
      '175' => array( '','E553' ),
      '176' => array( '','E594' ),
      '177' => array( '','E588' ),
      '178' => array( '','E588' ),
      '179' => array( '','E5B6' ),
      '180' => array( '','E504' ),
      '181' => array( '','E509' ),
      '182' => array( '','EB77' ),
      '183' => array( '','E4B8' ),
      '184' => array( '','E512' ),
      '185' => array( '','E4AB' ),
      '186' => array( '','E4C7' ),
      '187' => array( '','E5B8' ),
      '188' => array( '','EB78' ),
      '189' => array( '','E587' ),
      '190' => array( '','E4A1' ),
      '191' => array( '','E5C9' ),
      '192' => array( '','E514' ),
      '193' => array( '','E47C' ),
      '194' => array( '','E4AE' ),
      '195' => array( '','EAAE' ),
      '196' => array( '','E57A' ),
      '197' => array( '','EAC0' ),
      '198' => array( '','EAC5' ),
      '199' => array( '','E5C6' ),
      '200' => array( '','E5C6' ),
      '201' => array( '','EB5D' ),
      '202' => array( '','EAC9' ),
      '203' => array( '','E5C4' ),
      '204' => array( '','E4F9' ),
      '205' => array( '','E4E7' ),
      '206' => array( '','E5C3' ),
      '207' => array( '','EAC5' ),
      '208' => array( '','EAC2' ),
      '209' => array( '','EABF' ),
      '210' => array( '','E473' ),
      '211' => array( '','EB69' ),
      '212' => array( '','E551' ),
      '213' => array( '','E4A0' ),
      '214' => array( '','E558' ),
      '215' => array( '','E54E' ),
      '216' => array( '','E46B' ),
      '217' => array( '','E4F1' ),
      '218' => array( '','EB79' ),
      '219' => array( '','E559' ),
      '220' => array( '','E481' ),
      '221' => array( '','E541' ),
      '222' => array( '','EA8A' ),
      '223' => array( '','E4F0' ),
      '224' => array( '','EA89' ),
      '225' => array( '','EB7A' ),
      '226' => array( '','EB7B' ),
      '227' => array( '','EA80' ),
      '228' => array( '','EB7C' ),
      '229' => array( '','E5BD' ),
      '230' => array( '','E513' ),
      '231' => array( '','E4D2' ),
      '232' => array( '','E4E4' ),
      '233' => array( '','EB35' ),
      '234' => array( '','EAB9' ),
      '235' => array( '','EB7D' ),
      '236' => array( '','E4CE' ),
      '237' => array( '','E4CA' ),
      '238' => array( '','E4D5' ),
      '239' => array( '','E4D0' ),
      '240' => array( '','EA97' ),
      '241' => array( '','E5B4' ),
      '242' => array( '','EAAF' ),
      '243' => array( '','EB7E' ),
      '244' => array( '','E4E0' ),
      '245' => array( '','E4DC' ),
      '246' => array( '','E49A' ),
      '247' => array( '','EACD' ),
      '248' => array( '','EB80' ),
      '249' => array( '','E4D8' ),
      '250' => array( '','E4DE' ),
      '251' => array( '','E4C1' ),
      '252' => array( '','E5C5' ),
      '253' => array( '','E480' )
);

// 機種別絵文字一覧(SOFTBANK)
$L_MOBILE_EMOJI_SOFTBANK = array(
      '1' => array( '','E04A' ),
      '2' => array( '','E049' ),
      '3' => array( '','E04B' ),
      '4' => array( '','E048' ),
      '5' => array( '','E13D' ),
      '6' => array( '','E443' ),
      '7' => array( '','E049' ),
      '8' => array( '','E43C' ),
      '9' => array( '','E23F' ),
      '10' => array( '','E240' ),
      '11' => array( '','E241' ),
      '12' => array( '','E242' ),
      '13' => array( '','E243' ),
      '14' => array( '','E244' ),
      '15' => array( '','E245' ),
      '16' => array( '','E246' ),
      '17' => array( '','E247' ),
      '18' => array( '','E248' ),
      '19' => array( '','E249' ),
      '20' => array( '','E24A' ),
      '21' => array( '','E319' ),
      '22' => array( '','E016' ),
      '23' => array( '','E014' ),
      '24' => array( '','E015' ),
      '25' => array( '','E018' ),
      '26' => array( '','E013' ),
      '27' => array( '','E42A' ),
      '28' => array( '','E132' ),
      '29' => array( '','E128' ),
      '30' => array( '','E01E' ),
      '31' => array( '','E434' ),
      '32' => array( '','E435' ),
      '33' => array( '','E01B' ),
      '34' => array( '','E42E' ),
      '35' => array( '','E159' ),
      '36' => array( '','E202' ),
      '37' => array( '','E01D' ),
      '38' => array( '','E036' ),
      '39' => array( '','E038' ),
      '40' => array( '','E153' ),
      '41' => array( '','E155' ),
      '42' => array( '','E14D' ),
      '43' => array( '','E154' ),
      '44' => array( '','E158' ),
      '45' => array( '','E156' ),
      '46' => array( '','E03A' ),
      '47' => array( '','E14F' ),
      '48' => array( '','E14E' ),
      '49' => array( '','E151' ),
      '50' => array( '','E043' ),
      '51' => array( '','E045' ),
      '52' => array( '','E044' ),
      '53' => array( '','E047' ),
      '54' => array( '','E120' ),
      '55' => array( '','E13E' ),
      '56' => array( '','E313' ),
      '57' => array( '','E03C' ),
      '58' => array( '','E03D' ),
      '59' => array( '','E236' ),
      '60' => array( '','E124' ),
      '61' => array( '','E30A' ),
      '62' => array( '','E502' ),
      '63' => array( '','E503' ),
      '64' => array( '','E506' ),
      '65' => array( '','E125' ),
      '66' => array( '','E30E' ),
      '67' => array( '','E208' ),
      '68' => array( '','E008' ),
      '69' => array( '','E323' ),
      '70' => array( '','E148' ),
      '71' => array( '','E314' ),
      '72' => array( '','E112' ),
      '73' => array( '','E34B' ),
      '74' => array( '','E009' ),
      '75' => array( '','E00A' ),
      '76' => array( '','E301' ),
      '77' => array( '','E12A' ),
      '78' => array( '','E12B' ),
      '79' => array( '','E126' ),
      '80' => array( '','E20C' ),
      '81' => array( '','E20E' ),
      '82' => array( '','E20D' ),
      '83' => array( '','E20F' ),
      '84' => array( '','E419' ),
      '85' => array( '','E41B' ),
      '86' => array( '','E010' ),
      '87' => array( '','E011' ),
      '88' => array( '','E012' ),
      '89' => array( '','E238' ),
      '90' => array( '','E237' ),
      '91' => array( '','E536' ),
      '92' => array( '','E007' ),
      '93' => array( '','E419' ),
      '94' => array( '','E20A' ),
      '95' => array( '','E219' ),
      '96' => array( '','E04C' ),
      '97' => array( '','E04C' ),
      '98' => array( '','E04C' ),
      '99' => array( '','E332' ),
      '100' => array( '','E052' ),
      '101' => array( '','E04F' ),
      '102' => array( '','E01C' ),
      '103' => array( '','E033' ),
      '104' => array( '','E239' ),
      '105' => array( '','E104' ),
      '106' => array( '','E103' ),
      '107' => array( '','E00B' ),
      '108' => array( '','E00A' ),
      '109' => array( '','E00A' ),
      '110' => array( '','E103' ),
      '111' => array( '','E537' ),
      '112' => array( '','E537' ),
      '113' => array( '','E12F' ),
      '114' => array( '','E216' ),
      '115' => array( '','E229' ),
      '116' => array( '','E03F' ),
      '117' => array( '','E235' ),
      '118' => array( '','E23B' ),
      '119' => array( '','E114' ),
      '120' => array( '','E212' ),
      '121' => array( '','E12B' ),
      '122' => array( '','E211' ),
      '123' => array( '','E210' ),
      '124' => array( '','E336' ),
      '125' => array( '','E21C' ),
      '126' => array( '','E21D' ),
      '127' => array( '','E21E' ),
      '128' => array( '','E21F' ),
      '129' => array( '','E220' ),
      '130' => array( '','E221' ),
      '131' => array( '','E222' ),
      '132' => array( '','E223' ),
      '133' => array( '','E224' ),
      '134' => array( '','E225' ),
      '135' => array( '','E24D' ),
      '136' => array( '','E022' ),
      '137' => array( '','E327' ),
      '138' => array( '','E023' ),
      '139' => array( '','E327' ),
      '140' => array( '','E057' ),
      '141' => array( '','E059' ),
      '142' => array( '','E058' ),
      '143' => array( '','E407' ),
      '144' => array( '','E406' ),
      '145' => array( '','E236' ),
      '146' => array( '','E03E' ),
      '147' => array( '','E123' ),
      '148' => array( '','E206' ),
      '149' => array( '','E003' ),
      '150' => array( '','E32E' ),
      '151' => array( '','E10F' ),
      '152' => array( '','E334' ),
      '153' => array( '','E00D' ),
      '154' => array( '','E311' ),
      '155' => array( '','E326' ),
      '156' => array( '','E238' ),
      '157' => array( '','E13C' ),
      '158' => array( '','E021' ),
      '159' => array( '','E336' ),
      '160' => array( '','E337' ),
      '161' => array( '','E330' ),
      '162' => array( '','E331' ),
      '163' => array( '','E331' ),
      '164' => array( '','E330' ),
      '165' => array( '','E330' ),
      '166' => array( '','E330' ),
      '167' => array( '','E324' ),
      '168' => array( '','E12F' ),
      '169' => array( '','E301' ),
      '170' => array( '','E233' ),
      '171' => array( '','E11F' ),
      '172' => array( '','E44B' ),
      '173' => array( '','E234' ),
      '174' => array( '','E23C' ),
      '175' => array( '','E235' ),
      '176' => array( '','E02D' ),
      '177' => array( '','E00A' ),
      '178' => array( '','E00A' ),
      '179' => array( '','E006' ),
      '180' => array( '','E12F' ),
      '181' => array( '','E31C' ),
      '182' => array( '','E006' ),
      '183' => array( '','E013' ),
      '184' => array( '','E325' ),
      '185' => array( '','E036' ),
      '186' => array( '','E12F' ),
      '187' => array( '','E00C' ),
      '188' => array( '','E103' ),
      '189' => array( '','E00C' ),
      '190' => array( '','E301' ),
      '191' => array( '','E10E' ),
      '192' => array( '','E034' ),
      '193' => array( '','E026' ),
      '194' => array( '','E136' ),
      '195' => array( '','E338' ),
      '196' => array( '','E027' ),
      '197' => array( '','E403' ),
      '198' => array( '','E40A' ),
      '199' => array( '','E331' ),
      '200' => array( '','E108' ),
      '201' => array( '','E416' ),
      '202' => array( '','E40E' ),
      '203' => array( '','E106' ),
      '204' => array( '','E00E' ),
      '205' => array( '','E105' ),
      '206' => array( '','E405' ),
      '207' => array( '','E40A' ),
      '208' => array( '','E406' ),
      '209' => array( '','E402' ),
      '210' => array( '','E411' ),
      '211' => array( '','E413' ),
      '212' => array( '','E333' ),
      '213' => array( '','E301' ),
      '214' => array( '','E24E' ),
      '215' => array( '','E537' ),
      '216' => array( '','E115' ),
      '217' => array( '','E315' ),
      '218' => array( '','E332' ),
      '219' => array( '','E24F' ),
      '220' => array( '','E252' ),
      '221' => array( '','E333' ),
      '222' => array( '','E22B' ),
      '223' => array( '','E30D' ),
      '224' => array( '','E22A' ),
      '225' => array( '','E231' ),
      '226' => array( '','E233' ),
      '227' => array( '','E157' ),
      '228' => array( '','E43E' ),
      '229' => array( '','E03B' ),
      '230' => array( '','E110' ),
      '231' => array( '','E306' ),
      '232' => array( '','E304' ),
      '233' => array( '','E349' ),
      '234' => array( '','E345' ),
      '235' => array( '','E110' ),
      '236' => array( '','E118' ),
      '237' => array( '','E030' ),
      '238' => array( '','E342' ),
      '239' => array( '','E046' ),
      '240' => array( '','E30B' ),
      '241' => array( '','E340' ),
      '242' => array( '','E339' ),
      '243' => array( '','E441' ),
      '244' => array( '','E523' ),
      '245' => array( '','E055' ),
      '246' => array( '','E019' ),
      '247' => array( '','E056' ),
      '248' => array( '','E404' ),
      '249' => array( '','E01A' ),
      '250' => array( '','E10B' ),
      '251' => array( '','E044' ),
      '252' => array( '','E107' ),
      '253' => array( '','E209' )
);

//+++++++++++++++++++++++++++++
// MOBILE クラス
//+++++++++++++++++++++++++++++
class class_mobile{
//-----------------------------
// 定義
//-----------------------------
    var $L_MOBILE_IMAGE_PATH = "../../php_lib/class/mobile_images";
    // 許可するスタイル
    var $L_CSS_STYLE_ALLOWS = array(
        "color",
        "font-weight",
        "border",
    );
//-----------------------------
// キャリアごとの差異をなくす
//-----------------------------
      function _differenceCareer_callback($args){
            return $args[1].$args[2].floor(intval($args[3]) * 2.2).$args[4];
      }
      function differenceCareer($template){
            // ソフトバンクの場合画像が小さくなるので大きく表示
            if(get_carrier() == SYSTEM_CARRIER_SOFTBANK){
                  $preg_str = "/(<.+?)(width=[\"'])([0-9]+)([\"'].*?\/?>)/";
                  $template = preg_replace_callback( $preg_str , array($this, '_differenceCareer_callback') , $template );
                  $preg_str = "/(<.+?)(height=[\"'])([0-9]+)([\"'].*?\/?>)/";
                  $template = preg_replace_callback( $preg_str , array($this, '_differenceCareer_callback') , $template );
            }
            return $template;
      }
//-----------------------------
// 携帯絵置換マップを取得
//-----------------------------
      function get_convertPictogramMap($agent){
      GLOBAL $L_MOBILE_EMOJI_DOCOMO;
      GLOBAL $L_MOBILE_EMOJI_AU;
      GLOBAL $L_MOBILE_EMOJI_SOFTBANK;
            // 変換マップの取得
            $map = false;
            if($agent == SYSTEM_CARRIER_DOCOMO){
                  $map = &$L_MOBILE_EMOJI_DOCOMO;
            }else if($agent == SYSTEM_CARRIER_KDDI){
                  $map = &$L_MOBILE_EMOJI_AU;
            }else if($agent == SYSTEM_CARRIER_SOFTBANK){
                  $map = &$L_MOBILE_EMOJI_SOFTBANK;
            }
            return $map;
      }
//-----------------------------
// 携帯絵文字を画像で表示
//-----------------------------
      function convertPictogramImage($temptale,$src=SYSTEM_CARRIER_DOCOMO){
            $path = $this->L_MOBILE_IMAGE_PATH;
            // 変換マップの取得
            $src_map = $this->get_convertPictogramMap($src);
            // 置き換え
            if(is_array($src_map)){
                  foreach($src_map as $key => $val){
                        if($val[0] != ""){
                              // 置換対象文字
                              $target = "&#".$val[0].";";
                              $replace = '<img src="'.$path."/".$key.'.gif" width="12" height="12" alt="" />';
                              // キャリアごとに置換
                              $temptale = str_replace($target,$replace,$temptale);
                        }
                  }
            }
            return $temptale;
      }
//-----------------------------
// キャリアごとの絵文字変換
//-----------------------------
      function convertPictogramEmoji($temptale,$to,$src=SYSTEM_CARRIER_DOCOMO){
            // 変換元マップの取得
            $src_map = $this->get_convertPictogramMap($src);
            // 変換先マップの取得
            $to_map = $this->get_convertPictogramMap($to);
            // 置き換え
            if(is_array($src_map) && is_array($to_map)){
                  foreach($src_map as $key => $val){
                        if($val[0] != ""){
                              // 置換対象文字
                              $target = "&#".$val[0].";";
                              $replace = "&#x".$to_map[$key][1].";";
                              // キャリアごとに置換
                              $temptale = str_replace($target,$replace,$temptale);
                        }
                  }
            }
            return $temptale;
      }
//-----------------------------
// キャリアごとの絵文字変換
//-----------------------------
      function convertPictogram($temptale,$src=SYSTEM_CARRIER_DOCOMO){
            $to = get_agent_carrier();
//       if($to != $src){
                  // PC
                  if($to == SYSTEM_CARRIER_PC){
                        $temptale = $this->convertPictogramImage($temptale,$src);
                  // WillCom
                  }else if($to == SYSTEM_CARRIER_WILLCOM){
                        $temptale = $this->convertPictogramImage($temptale,$src);
                  // 携帯
                  }else{
                        $temptale = $this->convertPictogramEmoji($temptale,$to,$src);
                  }
//       }
            return $temptale;
      }
//-----------------------------
// 絵文字を取り除く
//-----------------------------
      function delete($temptale){
            $s = mb_substitute_character();
            mb_substitute_character('none');
            $temptale = mb_convert_encoding($temptale, SYSTEM_MOBILE_ENCODE, SYSTEM_MOBILE_ENCODE);
            mb_substitute_character($s);
            return $temptale;
      }
//-----------------------------
// 携帯用HTML宣言
//-----------------------------
    function HTMLDocument($user_agent=null){
        if(!$user_agent){
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        $html = "";
        if(preg_match("/DoCoMo/", $user_agent)) {
            $html .= "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?>\n";
            $html .= "<!DOCTYPE html PUBLIC \"-//i-mode group (ja)//DTD XHTML i-XHTML(Locale/Ver.=ja/2.3) 1.0//EN\" \"i-xhtml_4ja_10.dtd\">\n";
            $html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ja\" xml:lang=\"ja\">\n";
            $html .= "<head>\n";
            $html .= "<meta http-equiv=\"Content-Type\" content=\"application/xhtml+xml; charset=Shift_JIS\" />\n";
            //$html .= "<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n";
        } elseif(preg_match("/J\-PHONE|Vodafone|MOT\-[CV]980|SoftBank/", $user_agent)) {
            $html .= "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?>\n";
            $html .= "<!DOCTYPE html PUBLIC \"-//J-PHONE//DTD XHTML Basic 1.0 Plus//EN\" \"xhtml-basic10-plus.dtd\">\n";
            $html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ja\" xml:lang=\"ja\">\n";
            $html .= "<head>\n";
            $html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Shift_JIS\" />\n";
            //$html .= "<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n";
        } elseif(preg_match("/KDDI\-/", $user_agent) || preg_match("/UP\.Browser/", $user_agent)) {
            $html .= "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?>\n";
            $html .= "<!DOCTYPE html PUBLIC \"-//OPENWAVE//DTD XHTML 1.0//EN\" \"http://www.openwave.com/DTD/xhtml-basic.dtd\">\n";
            $html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ja\" xml:lang=\"ja\">\n";
            $html .= "<head>\n";
            $html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Shift_JIS\" />\n";
            //$html .= "<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n";
        } else {
            $html .= "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?>\n";
            $html .= "<!DOCTYPE html PUBLIC \"-//i-mode group (ja)//DTD XHTML i-XHTML(Locale/Ver.=ja/2.3) 1.0//EN\" \"i-xhtml_4ja_10.dtd\">\n";
            $html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ja\" xml:lang=\"ja\">\n";
            $html .= "<head>\n";
            $html .= "<meta http-equiv=\"Content-Type\" content=\"application/xhtml+xml; charset=Shift_JIS\" />\n";
            //$html .= "<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n";
        }
        return $html;
    }
//-----------------------------
// CSS埋め込み
//-----------------------------
    function loadFile($filename){
        $fp = fopen($filename, 'r');
        if ($fp){
            $buffer = "";
            if (flock($fp, LOCK_SH)){
                while (!feof($fp)) {
                    $buffer .= fgets($fp);
                }
                flock($fp, LOCK_UN);
            }
            fclose($fp);
            
            return $buffer;
        }
        return null;
    }
    function getHeadCSSTemplate($css_style){
        $text = '';
        if($css_style != ""){
            $text = '';
            $text .= '<style type="text/css">';
            $text .= '<![CDATA['."\n";
            $text .= $css_style."\n";
            $text .= ']]>';
            $text .= '</style>'."\n";
        }
        return $text;
    }
    function getHeadCSSTemplateFromFile($filename){
        $head = "";
        if($buffer = $this->loadFile($filename)){
            $head = $this->getHeadCSSTemplate($buffer);
        }
        return $head;
    }
    // 未実装
    function insertInlineCSSTemplate($template,$css_style){
        if($css_style != ""){
        }
    }
    function insertInlineCSSTemplateFromFile($template,$filename){
        if($buffer = $this->loadFile($filename)){
            $template = $this->insertInlineCSSTemplate($template,$buffer);
        }
        return $template;
    }
    // インラインスタイルの削除
    function _replaceInlineStyleCallback($matchs){
        $styles = explode(";",$matchs[2]);
        $new_styles = array();
        foreach($styles as $v){
                $mt = explode(":",$v);
                if(count($mt) == 2){
                        $name = trim($mt[0]);
                        $value = trim($mt[1]);
                        if(in_array($name,$this->L_CSS_STYLE_ALLOWS)){
                                $new_styles[$name] = $value;
                        }
                }
        }
        $result = array();
        foreach($new_styles as $k => $v){
                $result[] = $k.":".$v;
        }
        return $matchs[1].implode(";",$result).$matchs[3];
    }
    function removeInlineStyle($template){
        // スタイル削除
        return preg_replace_callback('/(style=[\"])([^>]*?)([\"])/is', array($this, '_replaceInlineStyleCallback'), $template);
    }
    // モバイルデータの整形
    function strip_tag_br_html($template){
        // テーブルの始めのセルを置換
        $template = preg_replace("/<tr(.*?)>([\s\S]*?)<td(.*?)>([\s\S]*?)<\/td>/is","<tr$1>$2<th$3>$4</th>",$template);
        $template = preg_replace("/<tr(.*?)>([\s\S]*?)<\/tr>/is","<tr$1>$2</tr><div>&nbsp;</div>",$template);
        //$template = preg_replace("/(<li(.*?)>)(!・)/ui","$1・$2",$template);
        $template = preg_replace_callback('/(<li(.*?)>)(.+)/i',"replaceHTMLList",$template);
        $template = preg_replace("/<ul(.*?)>([\s\S]*?)<\/ul>/i","<div$1>$2</div>",$template);
        $template = preg_replace("/<li(.*?)>([\s\S]*?)<\/li>/i","<div$1>$2</div>",$template);
        $template = preg_replace("/<td(.*?)>([\s\S]*?)<\/td>/i","<div$1>$2</div>",$template);
        $template = preg_replace("/<th(.*?)>([\s\S]*?)<\/th>/i","<div$1><span style=\"font-weight:bold;\">$2</span></div>",$template);
        // リンク画像置換
        //$template = preg_replace("/<a(.*?)>(.*)<img(.+)src=[\"'](.+?)[\"'](.*)<\/a>/i","<a$1>$2$4$5</a>",$template);
        $template = strip_tags($template,'<br><div><p><a><span><img>');
        print $template;
    }
}

?>