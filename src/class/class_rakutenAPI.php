<?php
//============================================
// class_rakutenAPI.php
//============================================
//-------------------------------
// 楽天APIリクエスト
//-------------------------------
define('C_RAKUTEN_API_URL', 'http://api.rakuten.co.jp/rws/3.0/rest');
define('C_RAKUTEN_API_ENCODE', 'UTF-8');
// 楽天商品検索APIリクエスト
define('C_RAKUTEN_API_ITEMSEARCH_VERSION', '2010-09-15');
define('C_RAKUTEN_API_ITEMSEARCH_OPERATION', 'ItemSearch');
// 楽天ジャンル検索APIリクエスト
define('C_RAKUTEN_API_GENRESEARCH_VERSION', '2007-04-11');
define('C_RAKUTEN_API_GENRESEARCH_OPERATION', 'GenreSearch');
// 楽天商品コード検索APIリクエスト
define('C_RAKUTEN_API_CODESEARCH_VERSION', '2010-08-05');
define('C_RAKUTEN_API_CODESEARCH_OPERATION', 'ItemCodeSearch');

//+++++++++++++++++++++++++++++
// rakutenAPIクラス
//+++++++++++++++++++++++++++++
class class_rakutenAPI
{
    // ディベロッパーID
    var $DEVELOPER_ID = '';
    // アフィリエイトID
    var $AFFILIATE_ID = '';
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
//-----------------------------
// 商品検索
//-----------------------------
    function ItemSearch($request)
    {
        $api_params = array(
            'operation' => C_RAKUTEN_API_ITEMSEARCH_OPERATION,
            'version' => C_RAKUTEN_API_ITEMSEARCH_VERSION,
            'affiliateId' => $this->AFFILIATE_ID,
            'keyword' => "",
            'shopCode' => "",
            'genreId' => "",
            'catalogCode' => "",
            'hits' => "",
            'page' => "",
            'sort' => "",
            'minPrice' => "",
            'maxPrice' => "",
            'availability' => "",
            'field' => "",
            'carrier' => "",
            'imageFlag' => "",
            'orFlag' => "",
            'NGKeyword' => "",
            'genreInformationFlag' => "",
            'purchaseType' => "",
            'shipOverseasFlag' => "",
            'shipOverseasArea' => "",
            'asurakuFlag' => "",
            'asurakuArea' => "",
            'pointRateFlag' => "",
            'pointRate' => "",
            'postageFlag' => "",
            'creditCardFlag' => ""
        );
        // リクエストURL生成
        $api_url = C_RAKUTEN_API_URL . "?developerId=" . $this->DEVELOPER_ID;
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_RAKUTEN_API_ENCODE);
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
                        // サービス固有パラメーター
                        case 'ARG':
                            break;
                        // 共通パラメータ：Status
                        case 'STATUS':
                            if (isset($data['value'])) {
                                $datalist['STATUS'] = $data['value'];
                            }
                            break;
                        // 共通パラメータ：Statusに特化したメッセージ
                        case 'STATUSMSG':
                            if (isset($data['value'])) {
                                $datalist['STATUSMSG'] = $data['value'];
                            }
                            break;
                        // 全体情報：検索数
                        case 'COUNT':
                            if (isset($data['value'])) {
                                $datalist['COUNT'] = $data['value'];
                            }
                            break;
                        // 商品情報：ITEMタグ
                        case 'ITEM':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'ITEM' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['ITEMS']) == false || is_array($datalist['ITEMS']) == false) {
                                    $datalist['ITEMS'] = array();
                                }
                                array_push($datalist['ITEMS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value'])) {
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
// ジャンル検索
//-----------------------------
    function genreSearch($request)
    {
        $api_params = array(
            'operation' => C_RAKUTEN_API_GENRESEARCH_OPERATION,
            'version' => C_RAKUTEN_API_GENRESEARCH_VERSION,
            'affiliateId' => $this->AFFILIATE_ID,
            'genreId' => "0",
            'genrePath' => ""
        );
        // リクエストURL生成
        $api_url = C_RAKUTEN_API_URL . "?developerId=" . $this->DEVELOPER_ID;
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_RAKUTEN_API_ENCODE);
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
                        // サービス固有パラメーター
                        case 'ARG':
                            break;
                        // 共通パラメータ：Status
                        case 'STATUS':
                            if (isset($data['value'])) {
                                $datalist['STATUS'] = $data['value'];
                            }
                            break;
                        // 共通パラメータ：Statusに特化したメッセージ
                        case 'STATUSMSG':
                            if (isset($data['value'])) {
                                $datalist['STATUSMSG'] = $data['value'];
                            }
                            break;
                        // 親ジャンル
                        case 'PARENT':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'PARENT' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                $datalist['PARENT'] = $item_temp;
                                $item_temp = null;
                            }
                            break;
                        // 自ジャンル
                        case 'CURRENT':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'CURRENT' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                $datalist['CURRENT'] = $item_temp;
                                $item_temp = null;
                            }
                            break;
                        // 子ジャンル
                        case 'CHILD':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'CHILD' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['CHILDS']) == false || is_array($datalist['CHILDS']) == false) {
                                    $datalist['CHILDS'] = array();
                                }
                                array_push($datalist['CHILDS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value'])) {
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
// コード検索
//-----------------------------
    function codeSearch($request)
    {
        $api_params = array(
            'operation' => C_RAKUTEN_API_CODESEARCH_OPERATION,
            'version' => C_RAKUTEN_API_CODESEARCH_VERSION,
            'affiliateId' => $this->AFFILIATE_ID,
            'itemCode' => "0",
            'carrier' => "0"
        );
        // リクエストURL生成
        $api_url = C_RAKUTEN_API_URL . "?developerId=" . $this->DEVELOPER_ID;
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create(C_RAKUTEN_API_ENCODE);
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
                        // サービス固有パラメーター
                        case 'ARG':
                            break;
                        // 共通パラメータ：Status
                        case 'STATUS':
                            if (isset($data['value'])) {
                                $datalist['STATUS'] = $data['value'];
                            }
                            break;
                        // 共通パラメータ：Statusに特化したメッセージ
                        case 'STATUSMSG':
                            if (isset($data['value'])) {
                                $datalist['STATUSMSG'] = $data['value'];
                            }
                            break;
                        // 全体情報：検索数
                        case 'COUNT':
                            if (isset($data['value'])) {
                                $datalist['COUNT'] = $data['value'];
                            }
                            break;
                        // 商品情報：ITEMタグ
                        case 'ITEM':
                            if ($data['type'] == 'open') {
                                $item_temp = array();
                                $i++;
                                while ($i < $count) {
                                    $data = $valuelist[$i];
                                    if (isset($data['tag'])) {
                                        // タグ終了
                                        if ($data['tag'] == 'ITEM' && $data['type'] == 'close') {
                                            break;
                                        }
                                        if (isset($data['value'])) {
                                            $item_temp[$data['tag']] = $data['value'];
                                        }
                                    }
                                    $i++;
                                }
                                if (isset($datalist['ITEMS']) == false || is_array($datalist['ITEMS']) == false) {
                                    $datalist['ITEMS'] = array();
                                }
                                array_push($datalist['ITEMS'], $item_temp);
                                $item_temp = null;
                            }
                            break;
                        default:
                            if (isset($data['value'])) {
                                $datalist[$data['tag']] = $data['value'];
                            }
                            break;
                    }
                }
            }
        }
        return $datalist;
    }
}

?>