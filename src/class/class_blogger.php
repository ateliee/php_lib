<?php
// ========================================
// ブログ投稿クラス
// class_blogger.php
// ========================================
//   対応ブログ
//    投 | 画 | サービス名
//   -------------------------
//    ○ | 未 | アメーバ
//    × | 未 | エキサイト
//    ○ | 未 | Fc2
//    ○ | × | ライブドア(2011/09現在画像投稿廃止されたようです)
//    未 | 未 | MovableType(未検証)
//    ○ | 未 | Wordpress
// ========================================
require_once(dirname(__FILE__) . "/../library/XML/RPC.php");
// PEAR
// PHP 4.3.0 以降ではこの方法も使用できます。
set_include_path(dirname(__FILE__) . "/../library/PEAR/" . PATH_SEPARATOR . get_include_path());
require_once("PEAR.php");
require_once("HTTP_Request/Request.php");

// エントリポイント
define("C_BLOG_AMEBA_URL", "http://atomblog.ameba.jp/servlet/_atom/blog");
define("C_BLOG_LIVEDOOR_URL", "http://livedoor.blogcms.jp/atom/blog");
define("C_BLOG_FC2_URL", "http://blog.fc2.com/xmlrpc.php");
define("C_BLOG_EXBLOG_URL", "exblog.jp/xmlrpc.php"); // {$USER_ID}.exblog.jp/xmlrpc.php

$GLOBALS['XML_RPC_defencoding'] = "UTF-8";

class class_blogger
{
    // メンバ変数
    var $username = "";
    var $password = "";
    var $entry_point = "";
    var $port = 80;
    var $debug = false;
    var $headers = array();

    // コンストラクタ
    function class_blogger($id = "", $pw = "")
    {
        if ($id != "" && $pw != "") {
            $this->setUser($id, $pw);
        }
    }

    // ユーザーID設定
    function setUser($id, $pw)
    {
        $this->username = $id;
        $this->password = $pw;
        $this->headers = array();
    }

    // エントリポイント設定
    function setEntryPoint($url)
    {
        $this->entry_point = $url;
    }

    // デバッグ
    function setDebug($debug)
    {
        $this->debug = $debug;
    }
    //-----------
    // ブログ投稿
    //-----------
    // 一括投稿
    function post($type, $postvalue)
    {
        switch ($type) {
            // Ameblog
            case SYSTEM_BLOG_AMEBA:
                return $this->postAmeba($postvalue);
                break;
            // FC2
            case SYSTEM_BLOG_FC2:
                return $this->postLivedoor($postvalue);
                break;
            // ライブドア
            case SYSTEM_BLOG_LIVEDOOR:
                return $this->postFC2($postvalue);
                break;
            // Wordpress
            case SYSTEM_BLOG_WORDPRESS:
                return $this->postWordpress($postvalue);
                break;
        }
        return false;
    }

    // アメーバ投稿
    function postAmeba($postvalue)
    {
        // エントリポイント設定
        $this->setEntryPoint(C_BLOG_AMEBA_URL);
        // 投稿用エンドポイント取得
        if ($url = $this->getPostUrl_Ameba()) {
            $encoding = mb_internal_encoding();

            $post_title = $postvalue["title"];
            $post_content = $postvalue["description"];
            $post_title = mb_substr($post_title, 0, 48, $encoding);
            $post_content = mb_substr($post_content, 0, 40000, $encoding);
            if ($post_title == "") {
                $post_title = "　";
            }
            if ($post_content == "") {
                $post_content = "　";
            }
            // 投稿用XML作成
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>' .
                '<entry xmlns="http://purl.org/atom/ns#" xmlns:app="http://www.w3.org/2007/app#" xmlns:mt="http://www.movabletype.org/atom/ns#">' .
                '<title>' . $post_title . '</title>' .
                '<content type="application/xhtml+xml"><![CDATA[' . $post_content . ']]></content>' .
                '</entry>';
            // 投稿
            $result = $this->requestSend($url, "POST", $this->getPostUrl_Ameba(), $xml);
            if ($result) {
                if (preg_match("/<error>(.*)<\/error>/", $result)) {
                    return false;
                } else {
                    return $result;
                }
            }
        }
        return false;
    }

    // livedoor投稿
    function postLivedoor($postvalue)
    {
        // エントリポイント設定
        $this->setEntryPoint(C_BLOG_LIVEDOOR_URL);
        // 投稿用エンドポイント取得
        if ($url = $this->getPostUrl_Livedoor()) {
            // 投稿用XML作成
            $text64 = base64_encode($postvalue["description"]);
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>' .
                '<entry xmlns="http://purl.org/atom/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/">' .
                '<title type="text/html" mode="escaped">' . $postvalue["title"] . '</title>' .
                //'<dc:subject type="text/html" mode="escaped">'.$category.'</dc:subject>'.
                '<content type="application/xhtml+xml" mode="base64">' . $text64 . '</content>' .
                '</entry>';
            // 投稿
            $result = $this->requestPost($url, $this->makeHeader_Livedoor(), $xml);
            if ($result) {
                return $result;
            }
        }
        return false;
    }

    // FC2投稿
    function postFC2($postvalue)
    {
        // 文字コードをEUC-JPで統一
        foreach ($postvalue as $key => $val) {
            $postvalue[$key] = mb_convert_encoding($val, "EUC-JP", "UTF-8");
        }
        // エントリポイント設定
        $this->setEntryPoint(C_BLOG_FC2_URL);
        // ホスト名とディレクトリに分割
        $parse_url = parse_url($this->entry_point);
        $host = $parse_url["host"];
        $path = $parse_url["path"];
        // XML_RPCクライアント作成
        $client = new XML_RPC_client($path, $host, $this->port);
        $client->setDebug($this->debug);

        // 送信データ
        $content = new XML_RPC_Value(array(
            'title' => new XML_RPC_Value($postvalue["title"], 'string'),
            'description' => new XML_RPC_Value($postvalue["description"], 'string'),
            'dateCreated' => new XML_RPC_Value(date("Ymd\TH:i:s", time()), 'dateTime.iso8601')
        ), 'struct');
        $blogid = new XML_RPC_Value(0, 'string');
        $username = new XML_RPC_Value($this->username, 'string');
        $passwd = new XML_RPC_Value($this->password, 'string');
        $publish = new XML_RPC_Value(1, 'boolean');
        // XML-RPCメソッドのセット
        $message = new XML_RPC_Message('metaWeblog.newPost', array($blogid, $username, $passwd, $content, $publish));
        // メッセージ送信
        $result = $client->send($message);

        if (!$result) {
            if ($this->debug) {
                exit('Could not connect to the server.');
            }
            return false;
        } else if ($result->faultCode()) {
            if ($this->debug) {
                exit('XML-RPC fault (' . $result->faultCode() . '): ' . $result->faultString());
            }
            return false;
        }
        return $result;
    }

    // WORDPRESS投稿
    function postWordpress($postvalue)
    {
        if ($this->entry_point != "") {
            // ホスト名とディレクトリに分割
            $parse_url = parse_url($this->entry_point);
            $host = $parse_url["host"];
            $path = rtrim($parse_url["path"], "\\/") . "/xmlrpc.php";

            // XML_RPCクライアント作成
            $client = new XML_RPC_client($path, $host, $this->port);
            $client->setDebug($this->debug);

            // 送信データ
            $content = new XML_RPC_Value(array(
                'title' => new XML_RPC_Value($postvalue["title"], 'string'),
                'description' => new XML_RPC_Value($postvalue["description"], 'string'),
                'dateCreated' => new XML_RPC_Value(date("Ymd\TH:i:s", time()), 'dateTime.iso8601')
            ), 'struct');
            $blogid = new XML_RPC_Value(0, 'string');
            $username = new XML_RPC_Value($this->username, 'string');
            $passwd = new XML_RPC_Value($this->password, 'string');
            $publish = new XML_RPC_Value(1, 'boolean');
            // XML-RPCメソッドのセット
            $message = new XML_RPC_Message('metaWeblog.newPost', array($blogid, $username, $passwd, $content, $publish));
            // メッセージ送信
            $result = $client->send($message);

            if (!$result) {
                if ($this->debug) {
                    exit('Could not connect to the server.');
                }
                return false;
            } else if ($result->faultCode()) {
                if ($this->debug) {
                    exit('XML-RPC fault (' . $result->faultCode() . '): ' . $result->faultString());
                }
                return false;
            }
            return $result;
        }
        return false;
    }
    //-----------
    // Abeba
    //-----------
    // 投稿用エンドポイント取得
    function getPostUrl_Ameba()
    {
        // ヘッダー生成
        if (count($this->headers) > 0) {
            return $this->headers;
        }
        $headers = $this->makeHeader_Ameba();
        $result_data = false;
        if ($res = $this->requestSend($this->entry_point, "GET", $headers)) {
            // リクエスト
            //$res = $this->requestGet($this->entry_point,$headers);
            if (preg_match('/rel="service.post" type="application\/x\.atom\+xml" href="(.*?)"/', $res, $result)) {
                $result_data = $result[1];
            }
        }

        return false;
    }

    // HEADER生成
    function makeHeader_Ameba()
    {
        $created = date('Y-m-d\TH:i:s\Z');
        $nonce = sha1(md5(time()));
        $pass_digest = base64_encode(pack('H*', sha1($nonce . $created . strtolower(md5($this->password)))));

        $this->headers = array(
            'X-WSSE' => $this->makeWSSE($pass_digest, $nonce, $created),
        );
        return $this->headers;
    }
    //-----------
    // livedoor
    //-----------
    // 投稿用エンドポイント取得
    function getPostUrl_Livedoor()
    {
        return $this->entry_point . "/" . $this->username . '/article';
    }

    // HEADER生成
    function makeHeader_Livedoor()
    {
        $created = date('Y-m-d\TH:i:s\Z');
        $nonce = pack('H*', sha1(md5(time())));
        $pass_digest = base64_encode(pack('H*', sha1($nonce . $created . $this->password)));

        $this->headers = array(
            'X-WSSE' => $this->makeWSSE($pass_digest, $nonce, $created),
            'Expect' => ""
        );
        return $this->headers;
    }
    //-----------
    // 共通
    //-----------
    // HEADER生成
    function makeWSSE($pass_digest, $nonce, $created)
    {
        $wsse = 'UsernameToken Username="' . $this->username . '", ' .
            'PasswordDigest="' . $pass_digest . '", ' .
            'Nonce="' . base64_encode($nonce) . '",' .
            'Created="' . $created . '"';
        return $wsse;
    }

    function createHeaderText($headers)
    {
        $h = array();
        foreach ($headers as $key => $val) {
            $h[] = $key . ": " . $val;
        }
        return $h;
    }

    // リクエスト(POST)
    function requestPost($url, $headers, $rawdata)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->createHeaderText($headers));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    // リクエスト(GET)
    function requestGet($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->createHeaderText($headers));
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    // リクエスト(PEAR)
    function requestSend($url, $method, $headers, $rawdata = null)
    {
        // PEARのエラーを一時的に無効化
        $E = error_reporting();
        if (($E & E_STRICT) == E_STRICT) {
            error_reporting($E ^ E_STRICT);
        }
        $result = null;
        $request = new HTTP_Request($url);
        if (strtoupper($method) == "POST") {
            $request->setMethod(HTTP_REQUEST_METHOD_POST);
        } else {
            $request->setMethod(HTTP_REQUEST_METHOD_GET);
        }
        foreach ($headers as $key => $val) {
            $request->addHeader($key, $val);
        }
        if ($rawdata) {
            $request->addRawPostData($rawdata);
        }
        $request->sendRequest();
        if (floor($request->getResponseCode() / 100) == 2) {
            $result = $request->getResponseBody();
        }
        // error_reportingを元に戻す
        error_reporting($E);

        return $result;
    }
}

?>
