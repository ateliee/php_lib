<?php
//============================================
// class_wikipediaAPI.php
//============================================
//-------------------------------
// WikipediaAPI
//-------------------------------
define('C_WIKIPEDIA_API_SIMPLEAPI_URL', 'http://wikipedia.simpleapi.net/api');
define('C_WIKIPEDIA_API_HOST', 'wikipedia.org');
define('C_WIKIPEDIA_API_URL', 'http://jp.wikipedia.org/wiki');

//+++++++++++++++++++++++++++++
// class_wikipediaAPIクラス
//+++++++++++++++++++++++++++++
class class_wikipediaAPI
{
    var $LANGUAGE = "ja";
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
//-----------------------------
// キーワード検索
//-----------------------------
    // MediaWiki
    function api($request = array())
    {
        $url = "http://" . $this->LANGUAGE . "." . C_WIKIPEDIA_API_HOST . "/w/api.php?format=php";
        $query = http_build_query($request);
        if ($query != "") {
            $url .= "&" . $query;
        }
        // リクエストを送る
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "PHP/" . PHP_VERSION);

        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $result = ob_get_contents();
        ob_end_clean();

        return unserialize($result);
    }

    // HTML解析
    function replaceContentsHTML($str)
    {
        // リンク変換
        $preg_str = "/\[\[(.+?)\]\]/i";
        $str = preg_replace_callback($preg_str, array($this, '_replaceContentsCallback_URL'), $str);
        // 強調変換
        $preg_str = "/'''(.+?)'''/i";
        $str = preg_replace_callback($preg_str, array($this, '_replaceContentsCallback_Strong'), $str);
        $preg_str = "/''(.+?)''/i";
        $str = preg_replace_callback($preg_str, array($this, '_replaceContentsCallback_Em'), $str);
        $preg_str = "/<nowiki>(.+?)<\/nowiki>/i";
        $str = preg_replace_callback($preg_str, array($this, '_replaceContentsCallback_Nowiki'), $str);
        return $str;
    }

    function _replaceContentsCallback_URL($args)
    {
        $str = $args[1];
        // 画像
        if ($pos = strpos($str, ":")) {
            $list = explode(":", $str);
            return '<img src="' . $list[1] . '" alt="' . $list[0] . '">';
            // 内部リンク
        } else if ($pos = strpos($str, "|")) {
            $list = explode("|", $str);
            return '<a href="' . C_WIKIPEDIA_API_URL . "/" . $list[0] . '">' . $list[1] . '</a>';
            // 外部リンク
        } else if ($pos = strpos($str, " ")) {
            $list1 = substr($str, 0, $pos);
            $list2 = substr($str, $pos);
            if (preg_match("/(https?|ftp)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/i", $list1)) {
                return '<a href="' . $list1 . '">' . $list2 . '</a>';
            }
        }
        return '<a href="' . C_WIKIPEDIA_API_URL . "/" . $str . '">' . $str . '</a>';
    }

    function _replaceContentsCallback_Strong($args)
    {
        $str = $args[1];
        return '<strong>' . $str . '</strong>';
    }

    function _replaceContentsCallback_Em($args)
    {
        $str = $args[1];
        return '<em>' . $str . '</em>';
    }

    function _replaceContentsCallback_Nowiki($args)
    {
        $str = $args[1];
        return $str;
    }

    // パース
    function parse($contents)
    {
        // 行分割
        $lines = explode(PHP_EOL, $contents);
        $tmp = array();
        $value = array();
        foreach ($lines as $line) {
            $type = false;
            // ?
            if (preg_match("/^[\s]*{{(.+)}}$/", $line, $matchs)) {
                $type = "extra";
                // INFO TABLE
            } else if (preg_match("/^[\s]*{{(.*)$/", $line, $matchs)) {
                $type = "info_table";
            } else if (preg_match("/^[\s]*}}/", $line, $matchs)) {
                $type = "info_table_close";
                // TABLE
            } else if (preg_match("/^[\s]*{\|(.*)$/", $line, $matchs)) {
                $type = "table";
            } else if (preg_match("/^[\s]*\|}/", $line, $matchs)) {
                $type = "table_close";
            } else if (preg_match("/^[\s]*\|\-(.*)$/", $line, $matchs)) {
                $type = "table_tr";
            } else if (preg_match("/^[\s]*\|(.*)$/", $line, $matchs)) {
                $type = "table_td";
            } else if (preg_match("/^[\s]*\!(.*)$/", $line, $matchs)) {
                $type = "table_th";
                // 見出し
            } else if (preg_match("/^====(.+)====$/", $line, $matchs)) {
                $type = "h4";
            } else if (preg_match("/^===(.+)===$/", $line, $matchs)) {
                $type = "h3";
            } else if (preg_match("/^==(.+)==$/", $line, $matchs)) {
                $type = "h2";
                // リスト
            } else if (preg_match("/^\*(.+)$/", $line, $matchs)) {
                $type = "list";
                // 文字下げ
            } else if (preg_match("/^;(.+)$/", $line, $matchs)) {
                $type = "dt";
            } else if (preg_match("/^:(.+)$/", $line, $matchs)) {
                $type = "dd";
                // テキスト
            } else {
                $type = "text";
            }
            //print $type." ".$line."<br>";
            if ($type) {
                if ($type != "list" && (isset($tmp["type"]) && ($tmp["type"] == "list"))) {
                    $value[] = $tmp;
                    $tmp = array();
                } else if ($type != "text" && (isset($tmp["type"]) && ($tmp["type"] == "text"))) {
                    $value[] = $tmp;
                    $tmp = array();
                } else if (($type != "dt" && $type != "dd") && (isset($tmp["type"]) && ($tmp["type"] == "dl"))) {
                    $value[] = $tmp;
                    $tmp = array();
                }
                // その他のタイプ
                if ($type == "extra") {
                    $tmp["type"] = $type;
                    $tmp["value"] = $matchs[1];
                    $value[] = $tmp;
                    $tmp = array();
                    // INFO TABLE
                } else if ($type == "info_table") {
                    $tmp["type"] = $type;
                    $tmp["attribute"] = $matchs[1];
                    $tmp["row"] = 0;
                    $tmp["value"] = array();
                    // TABLE
                } else if ($type == "table") {
                    $tmp["type"] = $type;
                    $tmp["attribute"] = $matchs[1];
                    $tmp["row"] = 0;
                    $tmp["value"] = array();
                } else if ($type == "table_tr") {
                    if (isset($tmp["type"])) {
                        if ($tmp["type"] == "table") {
                            if (count($tmp["value"]) > 0) {
                                $tmp["row"]++;
                            }
                        }
                    }
                } else if ($type == "table_td") {
                    if (isset($tmp["type"])) {
                        if ($tmp["type"] == "info_table") {
                            $t = array();
                            $t_val = explode("=", $matchs[1], 2);
                            foreach ($t_val as $key => $v) {
                                $t[] = trim($v);
                            }
                            $tmp["value"][] = $t;
                            $tmp["row"]++;
                        } else if ($tmp["type"] == "table") {
                            $t_val = explode("||", $matchs[1]);
                            foreach ($t_val as $key => $v) {
                                $tmp["value"][$tmp["row"]][] = array(
                                    "type" => "td",
                                    "value" => $v,
                                );
                            }
                        }
                    }
                } else if ($type == "table_th") {
                    if (isset($tmp["type"])) {
                        if ($tmp["type"] == "table") {
                            $tmp["value"][$tmp["row"]][] = array(
                                "type" => "th",
                                "value" => $matchs[1],
                            );
                        }
                    }
                } else if ($type == "info_table_close") {
                    if (isset($tmp["type"])) {
                        if ($tmp["type"] == "info_table") {
                            $value[] = $tmp;
                        }
                    }
                    $tmp = array();
                } else if ($type == "table_close") {
                    if (isset($tmp["type"])) {
                        if ($tmp["type"] == "table") {
                            $value[] = $tmp;
                        }
                    }
                    $tmp = array();
                } else if ($type == "h4" || $type == "h3" || $type == "h2") {
                    $tmp["type"] = $type;
                    $tmp["value"] = $matchs[1];
                    $value[] = $tmp;
                    $tmp = array();
                } else if ($type == "list") {
                    if (!isset($tmp["type"])) {
                        $tmp["type"] = "list";
                        $tmp["value"] = array();
                    }
                    if ($tmp["type"] == "list") {
                        $tmp["value"][] = $matchs[1];
                    }
                } else if ($type == "dt" || $type == "dd") {
                    if (!isset($tmp["type"])) {
                        $tmp["type"] = "dl";
                        $tmp["value"] = array();
                    }
                    if ($tmp["type"] == "dl") {
                        $tmp["value"][] = array(
                            "type" => $type,
                            "value" => $matchs[1]
                        );
                    }
                } else if ($type == "text") {
                    if (!isset($tmp["type"])) {
                        $tmp["type"] = "text";
                        $tmp["value"] = "";
                    }
                    if ($tmp["type"] == "text") {
                        $tmp["value"] .= $line . PHP_EOL;
                    }
                }
            }
        }
        if (isset($tmp["type"])) {
            $value[] = $tmp;
        }
        return $value;
    }

    // Wiki内部リンクの取得
    function get_url($str)
    {
        return C_WIKIPEDIA_API_URL . "/" . $str;
    }

    // SimpleAPI使用
    function SimpleAPISearch($request)
    {
        $api_params = array(
            'keyword' => "",
            'q' => "",
            'output' => "php",
            'callback' => "",
            'lang' => "ja",
            'search' => "1"
        );
        // リクエストURL生成
        $api_url = C_WIKIPEDIA_API_SIMPLEAPI_URL . "?";
        $api_url .= $this->getParams($api_params, $request);
        // APIリクエスト
        $contents = file_get_contents($api_url);
        // シリアライズを変数に戻す
        $contents = unserialize($contents);
        return $contents;
    }
}

