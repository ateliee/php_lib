<?php
//============================================
// class_yahooAPI.php
//============================================
define('C_YAHOO_API_ENCODE', 'UTF-8');
//-------------------------------
// ウェブ検索リクエスト
//-------------------------------
define('C_YAHOO_API_WEBSEARCH', 'http://search.yahooapis.jp/WebSearchService/V1/webSearch'); // XML
define('C_YAHOO_API_IMAGESEARCH', 'http://search.yahooapis.jp/ImageSearchService/V1/imageSearch'); // XML
define('C_YAHOO_API_VIDEOSEARCH', 'http://search.yahooapis.jp/VideoSearchService/V1/videoSearch'); // XML
define('C_YAHOO_API_ASSISTSEARCH', 'http://search.yahooapis.jp/AssistSearchService/V1/webunitSearch'); // XML
define('C_YAHOO_API_BLOGSEARCH', 'http://search.yahooapis.jp/BlogSearchService/V1/blogSearch'); // XML
//-------------------------------
// ショッピング検索リクエスト
//-------------------------------
define('C_YAHOO_API_SHOPPING_CATEGORYSEARCH', 'http://shopping.yahooapis.jp/ShoppingWebService/V1/php/categorySearch'); // PHPseriarize
define('C_YAHOO_API_SHOPPING_ITEMSSEARCH', 'http://shopping.yahooapis.jp/ShoppingWebService/V1/php/itemSearch'); // PHPseriarize
define('C_YAHOO_API_SHOPPING_CODESEARCH', 'http://shopping.yahooapis.jp/ShoppingWebService/V1/php/itemLookup'); // PHPseriarize
//-------------------------------
// オークション検索リクエスト
//-------------------------------
define('C_YAHOO_API_AUCTIONS_CATEGORYSEARCH', 'http://auctions.yahooapis.jp/AuctionWebService/V2/php/categoryTree'); // PHPseriarize
define('C_YAHOO_API_AUCTIONS_ITEMSEARCH', 'http://auctions.yahooapis.jp/AuctionWebService/V2/php/categoryLeaf'); // PHPseriarize
//-------------------------------
// 日本語形態素解析API
//-------------------------------
define('C_YAHOO_API_JLP', 'http://jlp.yahooapis.jp/MAService/V1/parse');

//+++++++++++++++++++++++++++++
// yahooAPIクラス
//+++++++++++++++++++++++++++++
class class_yahooAPI
{
    // アプリケーションID
    var $APPID = '';
    var $LANGUAGE = 'ja';
    // アフィリエイトID
    var $AFFILIATE_TYPE = 'yid';
    var $AFFILIATE_ID = '';

//-----------------------------
// アプリケーションIDの設定
//-----------------------------
    function setAPPID($id)
    {
        $this->APPID = $id;
    }
//-----------------------------
// パラメーター取得
//-----------------------------
    function getParams($api_params, $request)
    {
        $api_url = "";
        foreach ($api_params as $key => $param) {
            if (isset($request[$key]) && $request[$key] != "") {
                // リクエストパラメタにあれば、APIへのURLに追加
                $api_url .= "&" . $key . "=" . urlencode($request[$key]);
                $api_params[$key] = $request[$key];
            } elseif ($param != "") {
                // パラメタにあれば、APIへのURLに追加
                $api_url .= "&" . $key . "=" . urlencode($param);
            }
        }
        return $api_url;
    }

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
//====================================================
// 検索API
//====================================================
//-----------------------------
// ウェブ検索リクエスト
//-----------------------------
    function WebSearch($request)
    {
        $api_params = array(
            'query' => "",
            'type' => "",
            'results' => "",
            'start' => "",
            'format' => "",
            'adult_ok' => "",
            'similar_ok' => "",
            'language' => "",
            'country' => "",
            'site' => ""
        );
        $api_url = C_YAHOO_API_WEBSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);

        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_YAHOO_API_ENCODE);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);

        // データ
        $datalist = array();
        // 連想配列から値を取得
        if ($valuelist) {
            $count = count($valuelist);
            for ($i = 0; $i < $count; $i++) {
                $data = $valuelist[$i];
                if (isset($data['tag'])) {
                    // タグ名のよって分岐
                    switch ($data['tag']) {
                        // 全体情報
                        case 'RESULTSET':
                            if ($data['type'] == 'open') {
                                if (isset($data['attributes']) && is_array($data['attributes'])) {
                                    foreach ($data['attributes'] as $k => $d) {
                                        switch ($k) {
                                            // クエリにマッチした検索結果数
                                            case 'TOTALRESULTSAVAILABLE':
                                                $datalist['TOTALRESULTSAVAILABLE'] = $d;
                                                break;
                                            // 実際に返却された検索結果数
                                            case 'TOTALRESULTSRETURNED':
                                                $datalist['TOTALRESULTSRETURNED'] = $d;
                                                break;
                                            // 返却された検索結果の先頭の順位
                                            case 'FIRSTRESULTPOSITION':
                                                $datalist['FIRSTRESULTPOSITION'] = $d;
                                                break;
                                        }
                                    }
                                }
                            }
                            break;
                        // 検索情報
                        case 'RESULT':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'RESULT' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['RESULTS']) == false || is_array($datalist['RESULTS']) == false) {
                                    // 一覧
                                    $datalist['RESULTS'] = array();
                                }
                                array_push($datalist['RESULTS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value']) && is_array($data['value']) == false) {
                                $datalist[$data['tag']] = $data['value'];
                            }
                            break;
                    }
                }
            }
        }
        return $datalist;
    }
//-----------------------------
// 画像検索リクエスト
//-----------------------------
    function ImageSearch($request)
    {
        $api_params = array(
            'query' => "",
            'type' => "",
            'results' => "",
            'start' => "",
            'format' => "",
            'adult_ok' => "",
            'coloration' => "",
            'site' => ""
        );
        $api_url = C_YAHOO_API_IMAGESEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_YAHOO_API_ENCODE);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);

        // データ
        $datalist = array();
        // 連想配列から値を取得
        if ($valuelist) {
            $count = count($valuelist);
            for ($i = 0; $i < $count; $i++) {
                $data = $valuelist[$i];
                if (isset($data['tag'])) {
                    // タグ名のよって分岐
                    switch ($data['tag']) {
                        // 全体情報
                        case 'RESULTSET':
                            if ($data['type'] == 'open') {
                                if (isset($data['attributes']) && is_array($data['attributes'])) {
                                    foreach ($data['attributes'] as $k => $d) {
                                        switch ($k) {
                                            // クエリにマッチした検索結果数
                                            case 'TOTALRESULTSAVAILABLE':
                                                $datalist['TOTALRESULTSAVAILABLE'] = $d;
                                                break;
                                            // 実際に返却された検索結果数
                                            case 'TOTALRESULTSRETURNED':
                                                $datalist['TOTALRESULTSRETURNED'] = $d;
                                                break;
                                            // 返却された検索結果の先頭の順位
                                            case 'FIRSTRESULTPOSITION':
                                                $datalist['FIRSTRESULTPOSITION'] = $d;
                                                break;
                                        }
                                    }
                                }
                            }
                            break;
                        // 検索情報
                        case 'RESULT':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'RESULT' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['RESULTS']) == false || is_array($datalist['RESULTS']) == false) {
                                    // 一覧
                                    $datalist['RESULTS'] = array();
                                }
                                array_push($datalist['RESULTS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value']) && is_array($data['value']) == false) {
                                $datalist[$data['tag']] = $data['value'];
                            }
                            break;
                    }
                }
            }
        }
        return $datalist;
    }
//-----------------------------
// 動画検索リクエスト
//-----------------------------
    function VideoSearch($request)
    {
        $api_params = array(
            'query' => "",
            'type' => "",
            'results' => "",
            'start' => "",
            'format' => "",
            'adult_ok' => "",
            'site' => ""
        );
        $api_url = C_YAHOO_API_VIDEOSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_YAHOO_API_ENCODE);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);

        // データ
        $datalist = array();
        // 連想配列から値を取得
        if ($valuelist) {
            $count = count($valuelist);
            for ($i = 0; $i < $count; $i++) {
                $data = $valuelist[$i];
                if (isset($data['tag'])) {
                    // タグ名のよって分岐
                    switch ($data['tag']) {
                        // 全体情報
                        case 'RESULTSET':
                            if ($data['type'] == 'open') {
                                if (isset($data['attributes']) && is_array($data['attributes'])) {
                                    foreach ($data['attributes'] as $k => $d) {
                                        switch ($k) {
                                            // クエリにマッチした検索結果数
                                            case 'TOTALRESULTSAVAILABLE':
                                                $datalist['TOTALRESULTSAVAILABLE'] = $d;
                                                break;
                                            // 実際に返却された検索結果数
                                            case 'TOTALRESULTSRETURNED':
                                                $datalist['TOTALRESULTSRETURNED'] = $d;
                                                break;
                                            // 返却された検索結果の先頭の順位
                                            case 'FIRSTRESULTPOSITION':
                                                $datalist['FIRSTRESULTPOSITION'] = $d;
                                                break;
                                        }
                                    }
                                }
                            }
                            break;
                        // 検索情報
                        case 'RESULT':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'RESULT' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['RESULTS']) == false || is_array($datalist['RESULTS']) == false) {
                                    // 一覧
                                    $datalist['RESULTS'] = array();
                                }
                                array_push($datalist['RESULTS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value']) && is_array($data['value']) == false) {
                                $datalist[$data['tag']] = $data['value'];
                            }
                            break;
                    }
                }
            }
        }
        return $datalist;
    }
//-----------------------------
// 関連検索ワードリクエスト
//       query : （UTF-8エンコードされた）検索クエリーです。このクエリーはYahoo!検索の全言語をサポートし、またメタキーワードも含みます。
//-----------------------------
    function AssistSearch($request)
    {
        $api_params = array(
            'query' => "",
            'results' => "",
            'start' => ""
        );
        $api_url = C_YAHOO_API_ASSISTSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_YAHOO_API_ENCODE);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);

        // データ
        $datalist = array();
        // 連想配列から値を取得
        if ($valuelist) {
            $count = count($valuelist);
            for ($i = 0; $i < $count; $i++) {
                $data = $valuelist[$i];
                if (isset($data['tag'])) {
                    // タグ名のよって分岐
                    switch ($data['tag']) {
                        // 全体情報
                        case 'RESULTSET':
                            if ($data['type'] == 'open') {
                                if (isset($data['attributes']) && is_array($data['attributes'])) {
                                    foreach ($data['attributes'] as $k => $d) {
                                        switch ($k) {
                                            // クエリにマッチした検索結果数
                                            case 'TOTALRESULTSAVAILABLE':
                                                $datalist['TOTALRESULTSAVAILABLE'] = $d;
                                                break;
                                            // 実際に返却された検索結果数
                                            case 'TOTALRESULTSRETURNED':
                                                $datalist['TOTALRESULTSRETURNED'] = $d;
                                                break;
                                            // 返却された検索結果の先頭の順位
                                            case 'FIRSTRESULTPOSITION':
                                                $datalist['FIRSTRESULTPOSITION'] = $d;
                                                break;
                                        }
                                    }
                                }
                            }
                            break;
                        // 検索情報
                        case 'RESULT':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'RESULT' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['RESULTS']) == false || is_array($datalist['RESULTS']) == false) {
                                    // 一覧
                                    $datalist['RESULTS'] = array();
                                }
                                array_push($datalist['RESULTS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value']) && is_array($data['value']) == false) {
                                $datalist[$data['tag']] = $data['value'];
                            }
                            break;
                    }
                }
            }
        }
        return $datalist;
    }
//-----------------------------
// ブログ検索リクエスト
//       query : （UTF-8エンコードされた）検索クエリーです。このクエリーはYahoo!検索の全言語をサポートし、またメタキーワードも含みます。
//-----------------------------
    function BlogSearch($request)
    {
        $api_params = array(
            'query' => "",
            'type' => "",
            'results' => "",
            'start' => "",
            'term' => "",
            'output' => "",
            'callback' => ""
        );
        $api_url = C_YAHOO_API_BLOGSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_YAHOO_API_ENCODE);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);

        // データ
        $datalist = array();
        // 連想配列から値を取得
        if ($valuelist) {
            $count = count($valuelist);
            for ($i = 0; $i < $count; $i++) {
                $data = $valuelist[$i];
                if (isset($data['tag'])) {
                    // タグ名のよって分岐
                    switch ($data['tag']) {
                        // 全体情報
                        case 'RESULTSET':
                            if ($data['type'] == 'open') {
                                if (isset($data['attributes']) && is_array($data['attributes'])) {
                                    foreach ($data['attributes'] as $k => $d) {
                                        switch ($k) {
                                            // クエリにマッチした検索結果数
                                            case 'TOTALRESULTSAVAILABLE':
                                                $datalist['TOTALRESULTSAVAILABLE'] = $d;
                                                break;
                                            // 実際に返却された検索結果数
                                            case 'TOTALRESULTSRETURNED':
                                                $datalist['TOTALRESULTSRETURNED'] = $d;
                                                break;
                                            // 返却された検索結果の先頭の順位
                                            case 'FIRSTRESULTPOSITION':
                                                $datalist['FIRSTRESULTPOSITION'] = $d;
                                                break;
                                        }
                                    }
                                }
                            }
                            break;
                        // 検索情報
                        case 'RESULT':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'RESULT' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['RESULTS']) == false || is_array($datalist['RESULTS']) == false) {
                                    // 一覧
                                    $datalist['RESULTS'] = array();
                                }
                                array_push($datalist['RESULTS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value']) && is_array($data['value']) == false) {
                                $datalist[$data['tag']] = $data['value'];
                            }
                            break;
                    }
                }
            }
        }
        return $datalist;
    }
//====================================================
// ショッピング
//====================================================
//-----------------------------
// カテゴリ検索リクエスト
//-----------------------------
    function CategorySearch($request)
    {
        $api_params = array(
            'affiliate_type' => $this->AFFILIATE_TYPE,
            'affiliate_id' => $this->AFFILIATE_ID,
            'callback' => "",
            'category_id' => ""
        );
        $api_url = C_YAHOO_API_SHOPPING_CATEGORYSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);

        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        if ($contents) {
            // シリアライズを変数に戻す
            $contents = unserialize($contents);
            // データ
            $datalist = array();
            if (is_array($contents) && isset($contents['ResultSet'])) {
                $datalist = $contents['ResultSet'];
                if (is_array($datalist) && isset($datalist['0']) && isset($datalist['0']['Result'])) {
                    $datalist['Result'] = $datalist['0']['Result'];
                    unset($datalist['0']);
                }
            }
            return $datalist;
        }
        return false;
    }
//-----------------------------
// 商品検索リクエスト
//-----------------------------
    function ItemSearch($request)
    {
        $api_params = array(
            'affiliate_type' => $this->AFFILIATE_TYPE,
            'affiliate_id' => $this->AFFILIATE_ID,
            'callback' => "",
            'query' => "",
            'jan' => "",
            'isbn' => "",
            'category_id' => "",
            'product_id' => "",
            'person_id' => "",
            'brand_id' => "",
            'store_id' => "",
            'price_from' => "",
            'price_to' => "",
            'hits' => "",
            'offset' => "",
            'affiliate_from' => "",
            'affiliate_to' => "",
            'module' => "",
            'availability' => "",
            'discount' => "",
            'shipping' => ""
        );
        $api_url = C_YAHOO_API_SHOPPING_ITEMSSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);

        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        if ($contents) {
            // シリアライズを変数に戻す
            $contents = unserialize($contents);
            // データ
            $datalist = array();
            if (is_array($contents) && isset($contents['ResultSet'])) {
                $datalist = $contents['ResultSet'];
                // Resultを再配置
                if (isset($datalist['0'])) {
                    $datalist['Result'] = array();
                    if (isset($datalist['0']['Result'])) {
                        foreach ($datalist['0']['Result'] as $key => $val) {
                            if (is_numeric($key)) {
                                $datalist['Result'][] = $val;
                            } else {
                                $datalist[$key] = $val;
                            }
                        }
                    }
                    unset($datalist['0']);
                }
            }
            return $datalist;
        }
        return false;
    }
//-----------------------------
// 商品コード検索リクエスト
//-----------------------------
    function CodeSearch($request)
    {
        $api_params = array(
            'affiliate_type' => $this->AFFILIATE_TYPE,
            'affiliate_id' => $this->AFFILIATE_ID,
            'callback' => "",
            'itemcode' => "",
            'responsegroup' => ""
        );
        $api_url = C_YAHOO_API_SHOPPING_CODESEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);

        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        if ($contents) {
            // シリアライズを変数に戻す
            $contents = unserialize($contents);
            // データ
            $datalist = array();
            if (is_array($contents) && isset($contents['ResultSet'])) {
                $datalist = $contents['ResultSet'];
                // Resultを再配置
                if (isset($datalist['0'])) {
                    $datalist['Result'] = array();
                    if (isset($datalist['0']['Result'])) {
                        foreach ($datalist['0']['Result'] as $key => $val) {
                            if (is_numeric($key)) {
                                $datalist['Result'][] = $val;
                            } else {
                                $datalist[$key] = $val;
                            }
                        }
                    }
                    unset($datalist['0']);
                }
            }
            return $datalist;
        }
        return false;
    }
//====================================================
// オークション
//====================================================
//-----------------------------
// カテゴリ検索リクエスト
//-----------------------------
    function AuctionsCategorySearch($request)
    {
        $api_params = array(
            'callback' => "",
            'category' => ""
        );
        $api_url = C_YAHOO_API_AUCTIONS_CATEGORYSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);

        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        if ($contents) {
            // シリアライズを変数に戻す
            $contents = unserialize($contents);
            // データ
            $datalist = array();
            if (is_array($contents) && isset($contents['ResultSet'])) {
                $datalist = $contents['ResultSet'];
            }
            return $datalist;
        }
        return false;
    }
//-----------------------------
// 商品検索リクエスト
//-----------------------------
    function AuctionsItemSearch($request)
    {
        $api_params = array(
            'callback' => "",
            'category' => "",
            'page' => "",
            'sort' => "",
            'order' => "",
            'store' => "",
            'aucminprice' => "",
            'aucmaxprice' => "",
            'aucmin_bidorbuy_price' => "",
            'aucmax_bidorbuy_price' => "",
            'escrow' => "",
            'easypayment' => "",
            'ybank' => "",
            'new' => "",
            'freeshipping' => "",
            'wrappingicon' => "",
            'buynow' => "",
            'thumbnail' => "",
            'attn' => "",
            'english' => "",
            'point' => "",
            'gift_icon' => "",
            'item_status' => "",
            'offer' => ""
        );
        $api_url = C_YAHOO_API_AUCTIONS_ITEMSEARCH . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);

        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        if ($contents) {
            // シリアライズを変数に戻す
            $contents = unserialize($contents);
            // データ
            $datalist = array();
            if (is_array($contents) && isset($contents['ResultSet'])) {
                $datalist = $contents['ResultSet'];
            }
            return $datalist;
        }
        return false;
    }
//-----------------------------
// 日本語形態素解析API
//-----------------------------
    function JlpParse($request)
    {
        $api_params = array(
            'sentence' => "", // 解析対象のテキストです。
            'results' => "", // 解析結果の種類をコンマで区切って指定します。
            'response' => "", // ma_response, uniq_response のデフォルト設定です。word に返される形態素情報をコンマで区切って指定します。
            'filter' => "", // ma_filter, uniq_filter のデフォルト設定です。解析結果として出力する品詞番号を "｜" で区切って指定します。
            'ma_response' => "", // ma_result 内の word に返される形態素情報をコンマで区切って指定します。無指定の場合 response の指定が用いられます。
            'ma_filter' => "", // ma_result 内に解析結果として出力する品詞番号を "｜" で区切って指定します。無指定の場合 filter の指定が用いられます。
            'uniq_response' => "", // uniq_result 内の word に返される形態素情報をコンマで区切って指定します。無指定の場合 response の指定が用いられます。
            'uniq_filter' => "", // uniq_result 内に解析結果として出力する品詞番号を "｜" で区切って指定します。無指定の場合 filter の指定が用いられます。
            'uniq_by_baseform' => "", // このパラメータが true ならば、基本形の同一性により、uniq_result の結果を求めます。"
        );
        $api_url = C_YAHOO_API_JLP . "?appid=" . $this->APPID;
        $api_url .= $this->getParams($api_params, $request);

        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        if ($contents) {
            // オブジェクトに変換
            $objects = simplexml_load_string($contents);
            // 配列に変換
            $valuelist = $this->get_object_vars_recursive($objects);
            return $valuelist;
        }
        return false;
    }

    // 再帰的にオブジェクトを配列に変換
    function get_object_vars_recursive($obj)
    {
        if (is_object($obj)) {
            $value = get_object_vars($obj);
            foreach ($value as $k => $v) {
                $value[$k] = $this->get_object_vars_recursive($v);
            }
        } else if (is_array($obj)) {
            $value = array();
            foreach ($obj as $k => $v) {
                $value[$k] = $this->get_object_vars_recursive($v);
            }
        } else {
            $value = $obj;
        }
        return $value;
    }
}
