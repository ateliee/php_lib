<?php
//============================================
// class_amazonAPI.php
//============================================
//-------------------------------
// amazonAPI
//-------------------------------
define('C_AMAZON_API_METHOD', 'GET');
define('C_AMAZON_API_HOST', 'ecs.amazonaws.jp');
define('C_AMAZON_API_PATH', '/onca/xml');
define('C_AMAZON_API_URL', 'http://' . C_AMAZON_API_HOST . C_AMAZON_API_PATH);
define('C_AMAZON_API_ENCODE', 'UTF-8');
define('C_AMAZON_API_ITEMSEARCH_VERSION', '2009-07-01');
//-------------------------------
// amazonAPIカテゴリ
//-------------------------------
$C_AMAZON_API_CATEOGRY = array(
    'All' => '全て',
    'Apparel' => 'アパレル',
    'Baby' => 'ベビー＆マタニティ',
    'Beauty' => 'コスメ',
    'Blended' => '全て',
    'Books' => '本(和書)',
    'Classical' => 'クラシック音楽',
    'DVD' => 'DVD',
    'Electronics' => 'エレクトロニクス',
    'ForeignBooks' => '洋書',
    'Grocery' => '食品',
    'HealthPersonalCare' => 'ヘルスケア',
    'Hobbies' => 'ホビー',
    'Jewelry' => 'ジュエリー',
    'Kitchen' => 'ホーム＆キッチン',
    'Music' => 'ミュージック',
    'MusicTracks' => '曲名',
    'Software' => 'ソフトウェア',
    'SportingGoods' => 'スポーツ＆アウトドア',
    'Toys' => 'おもちゃ',
    'VHS' => 'VHS',
    'Video' => 'ビデオ',
    'VideoGames' => 'ゲーム',
    'Watches' => '時計'
);

//+++++++++++++++++++++++++++++
// class_amazonAPIクラス
//+++++++++++++++++++++++++++++
class class_amazonAPI
{
    // アクセスキー
    var $ACCESS_KEY = '';
    // プライベートキー
    var $PRIVATE_KEY = '';
    // アソシエイトキー
    var $ASSOCIATE_ID = '';
    //-----------------------------
    // パラメーター取得
    //-----------------------------

    function ItemSearch($request)
    {
        $api_params = array(
            'Service' => "AWSECommerceService",
            'AWSAccessKeyId' => $this->ACCESS_KEY,
            'Operation' => "ItemSearch",
            'Version' => C_AMAZON_API_ITEMSEARCH_VERSION,
            'AssociateTag' => $this->ASSOCIATE_ID,
            'SearchIndex' => 'All'
        );
        $request_param = $api_params;
        foreach ($request as $key => $val) {
            if ($val != "") {
                $request_param[$key] = $val;
            }
        }
        // リクエストURL生成
        $api_url = C_AMAZON_API_URL . "?" . $this->getParams($request_param);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_AMAZON_API_ENCODE);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);

        $results = $this->XMLParser($valuelist);
        if (isset($results["ITEMSEARCHRESPONSE"])) {
            return $results["ITEMSEARCHRESPONSE"];
        }
        return null;
    }

    function getParams($request)
    {
        $api_url = "";

        $request['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        // キーでソート
        ksort($request);

        $query = array();
        foreach ($request as $param => $value) {
            $param = str_replace("%7E", "~", rawurlencode($param));
            $value = str_replace("%7E", "~", rawurlencode($value));
            $query[] = $param . "=" . $value;
        }
        $canonical_string = implode("&", $query);

        // 認証キー元を作成
        $string_to_sign = C_AMAZON_API_METHOD . "\n" . C_AMAZON_API_HOST . "\n" . C_AMAZON_API_PATH . "\n" . $canonical_string;
        // HMAC with the SHA256ハッシュアルゴリズムを使って計算し署名を作成
        $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $this->PRIVATE_KEY, true));
        $signature = str_replace("%7E", "~", rawurlencode($signature));

        $api_url = $canonical_string . "&Signature=" . $signature;
        return $api_url;
    }
    //-----------------------------
    // XMLを処理
    //-----------------------------

    function file_get_contents($url, $timeout = 60)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function XMLParser(&$valuelist)
    {
        // データ
        $datalist = array();
        // 連想配列から値を取得
        if ($valuelist) {
            $count = count($valuelist);
            $level = 1;
            $new_level = 1;
            for ($i = 0; $i < $count; $i++) {
                $i = $this->_XMLParser_Array($i, $count, $valuelist, $new_level, $datalist);
            }
        }
        /*
        // データ
        $datalist = array();
        // 連想配列から値を取得
        if($valuelist){
              $count = count($valuelist);
              for($i=0;$i<$count;$i++) {
                    $data = $valuelist[$i];
                    if(isset($data['tag'])){
                          // タグ名のよって分岐
                          switch ($data['tag']) {
                          // 検索情報
                          case 'ITEM':
                                if($data['type'] == 'open'){
                                      $item_temp = array();
                                      $i ++;
                                      while($i < $count){
                                            $data = $valuelist[$i];
                                            if(isset($data['tag'])){
                                                  // タグ終了
                                                  if($data['tag'] == 'ITEM' && $data['type'] == 'close'){
                                                        break;
                                                  }
                                                  // 画像(小)
                                                  if($data['tag'] == 'SMALLIMAGE'){
                                                        if($data['type'] == 'open'){
                                                              $d = array();
                                                              $i ++;
                                                              while($i < $count){
                                                                    $data = $valuelist[$i];
                                                                    if(isset($data['tag'])){
                                                                          // タグ終了
                                                                          if($data['tag'] == 'SMALLIMAGE' && $data['type'] == 'close'){
                                                                                break;
                                                                          }
                                                                    }
                                                                    if(isset($data['value'])){
                                                                          $d[$data['tag']] = $data['value'];
                                                                    }
                                                                    $i ++;
                                                              }
                                                              $item_temp['SMALLIMAGE'] = $d;
                                                        }
                                                  // 画像(中)
                                                  }else if($data['tag'] == 'MEDIUMIMAGE'){
                                                        if($data['type'] == 'open'){
                                                              $d = array();
                                                              $i ++;
                                                              while($i < $count){
                                                                    $data = $valuelist[$i];
                                                                    if(isset($data['tag'])){
                                                                          // タグ終了
                                                                          if($data['tag'] == 'MEDIUMIMAGE' && $data['type'] == 'close'){
                                                                                break;
                                                                          }
                                                                    }
                                                                    if(isset($data['value'])){
                                                                          $d[$data['tag']] = $data['value'];
                                                                    }
                                                                    $i ++;
                                                              }
                                                              $item_temp['MEDIUMIMAGE'] = $d;
                                                        }
                                                  // 画像(大)
                                                  }else if($data['tag'] == 'LARGEIMAGE'){
                                                        if($data['type'] == 'open'){
                                                              $d = array();
                                                              $i ++;
                                                              while($i < $count){
                                                                    $data = $valuelist[$i];
                                                                    if(isset($data['tag'])){
                                                                          // タグ終了
                                                                          if($data['tag'] == 'LARGEIMAGE' && $data['type'] == 'close'){
                                                                                break;
                                                                          }
                                                                    }
                                                                    if(isset($data['value'])){
                                                                          $d[$data['tag']] = $data['value'];
                                                                    }
                                                                    $i ++;
                                                              }
                                                              $item_temp['LARGEIMAGE'] = $d;
                                                        }
                                                  }else if(isset($data['value'])){
                                                        $item_temp[$data['tag']] = $data['value'];
                                                  }
                                            }
                                            $i ++;
                                      }
                                      if(isset($datalist['ITEMS']) == false || is_array($datalist['ITEMS']) == false){
                                            // 一覧
                                            $datalist['ITEMS'] = array();
                                      }
                                      array_push($datalist['ITEMS'],$item_temp);
                                      $item_temp = null;
                                }
                                break;
                          default:
                                if(isset($data['value']) && is_array($data['value']) == false){
                                      $datalist[$data['tag']] = $data['value'];
                                }
                                break;
                          }
                    }
              }
        }*/
        return $datalist;
    }
    //-----------------------------
    // 商品検索
    //-----------------------------

    function _XMLParser_Array($i, $count, $valuelist, &$level, &$datalist)
    {
        $data = $valuelist[$i];
        if (isset($data["type"]) && ($data["type"] == "open")) {
            $before_level = $level;
            $level++;
            $i++;
            if (isset($datalist[$data["tag"]])) {
                if (!isset($datalist[$data["tag"]][0])) {
                    $d = array();
                    $d[] = $datalist[$data["tag"]];
                    $d[] = array();
                    $datalist[$data["tag"]] = $d;
                    $input = & $datalist[$data["tag"]][count($datalist[$data["tag"]]) - 1];
                } else {
                    $datalist[$data["tag"]][] = array();
                    $input = & $datalist[$data["tag"]][count($datalist[$data["tag"]]) - 1];
                }
            } else {
                $datalist[$data["tag"]] = array();
                $input = & $datalist[$data["tag"]];
            }
            for (; $i < $count; $i++) {
                $i = $this->_XMLParser_Array($i, $count, $valuelist, $level, $input);
                if ($before_level == $level) {
                    break;
                }
            }
        } else if (isset($data["type"]) && ($data["type"] == "close")) {
            $before_level = $level;
            $level--;
        } else {
            $datalist[$data["tag"]] = "";
            if (isset($data["value"])) {
                $datalist[$data["tag"]] = $data["value"];
            }
        }
        return $i;
    }
    //-----------------------------
    // 商品検索(ISBN/ASINなどのID検索)
    //-----------------------------

    function ItemLookup($request)
    {
        $api_params = array(
            'Service' => "AWSECommerceService",
            'AWSAccessKeyId' => $this->ACCESS_KEY,
            'Operation' => "ItemLookup",
            'Version' => C_AMAZON_API_ITEMSEARCH_VERSION,
            'AssociateTag' => $this->ASSOCIATE_ID
        );
        $request_param = $api_params;
        foreach ($request as $key => $val) {
            if ($val != "") {
                $request_param[$key] = $val;
            }
        }
        // リクエストURL生成
        $api_url = C_AMAZON_API_URL . "?" . $this->getParams($request_param);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_AMAZON_API_ENCODE);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);

        //return $this->XMLParser($valuelist);
        $results = $this->XMLParser($valuelist);
        if (isset($results["ITEMLOOKUPRESPONSE"])) {
            return $results["ITEMLOOKUPRESPONSE"];
        }
        return null;
    }
}