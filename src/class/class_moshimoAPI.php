<?php
//============================================
// class_moshimoAPI.php
//============================================
//-------------------------------
// 定義
//-------------------------------
define('C_MOSHIMO_API_SEARCH', 'http://api.moshimo.com/article/search');
define('C_MOSHIMO_API_SEARCH2', 'http://api.moshimo.com/article/search2');
define('C_MOSHIMO_API_SEARCH_CATEGORY', 'http://api.moshimo.com/category/list');
define('C_MOSHIMO_API_SEARCH_CATEGORY2', 'http://api.moshimo.com/category/list2');
define('C_MOSHIMO_API_SEARCH_MATERIAL', 'http://api.moshimo.com/article/material');
define('C_MOSHIMO_API_SEARCH_OPTION', 'http://api.moshimo.com/article/option');
define('C_MOSHIMO_API_SEARCH_REVIEW', 'http://api.moshimo.com/article/review');
define('C_MOSHIMO_API_SEARCH_REASON', 'http://api.moshimo.com/article/reason');
define('C_MOSHIMO_API_ENCODE', 'UTF-8');
define('C_MOSHIMO_API_ITEM_URL', 'http://mp.moshimo.com/article/%s?shop_id=%s');
define('C_MOSHIMO_API_CART_URL', 'http://mp.moshimo.com/cart?shop_id=%s');
define('C_MOSHIMO_API_CART_ADD_URL', 'http://mp.moshimo.com/cart/add?article_id=%s&shop_id=%s');
define('C_MOSHIMO_API_IMAGE_URL', 'http://image.moshimo.com/item_image/%s/%s/%s.jpg');
define('C_MOSHIMO_API_IMAGE_URL2', 'http://image.moshimo.com/static/img/mp/article/material/%s/%s/%s/%s.jpg');

//+++++++++++++++++++++++++++++
// class_moshimoAPIクラス
//+++++++++++++++++++++++++++++
class class_moshimoAPI
{
    // ショップID
    var $SHOP_ID = '';
    // APIキー
    var $APIKEY = '';
    // 変換モード(xmlもしくはjson)
    var $RESULT_TYPE = 'xml';
    //-----------------------------
    // パラメーター取得
    //-----------------------------
    function setShopID($id)
    {
        $this->SHOP_ID = $id;
        return $id;
    }

    function setAPIKey($id)
    {
        $this->APIKEY = $id;
        return $id;
    }

    function setResultType($type)
    {
        $this->RESULT_TYPE = $type;
        return $type;
    }

    function getItemUrl($id)
    {
        return sprintf(C_MOSHIMO_API_ITEM_URL, $id, $this->SHOP_ID);
    }

    function getCartAddUrl($id)
    {
        return sprintf(C_MOSHIMO_API_CART_ADD_URL, $id, $this->SHOP_ID);
    }

    function getCartUrl()
    {
        return sprintf(C_MOSHIMO_API_CART_URL, $this->SHOP_ID);
    }

    function getImageUrl($id, $no, $size)
    {
        return sprintf(C_MOSHIMO_API_IMAGE_URL, $id, $no, $size);
    }

    function getImageUrl2($id, $no)
    {
        $id_code = sprintf("%09d", $id);
        $id1 = substr($id_code, 0, 3);
        $id2 = substr($id_code, 3, 3);
        $id3 = substr($id_code, 6, 3);
        $no_code = sprintf("%02d", $no);
        return sprintf(C_MOSHIMO_API_IMAGE_URL2, $id1, $id2, $id3, $no_code);
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
    // リクエストを送る
    //-----------------------------
    function requestParam($url, $param = array())
    {
        $url = ($this->RESULT_TYPE != "") ? $url . "." . $this->RESULT_TYPE : $url;
        // 認証コード設定
        $param["authorization_code"] = $this->APIKEY;
        // リクエストURL生成
        $api_url = $url . "?" . http_build_query($param);
        // APIリクエスト
        $contents = $this->file_get_contents($api_url);
        return $contents;
    }
    //-----------------------------
    // 商品検索
    //-----------------------------
    function ItemSearch($request)
    {
        return $this->requestParam(C_MOSHIMO_API_SEARCH, $request);
    }
}
