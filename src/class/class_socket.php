<?php
//============================================
// class_socket.php
//============================================
//+++++++++++++++++++++++++++++
// 同期通信クラス
//+++++++++++++++++++++++++++++
class class_socket
{
    var $port = 80;
    var $header = array();
    //-----------------------------
    // 送信関数
    //-----------------------------
    function send($request_url, $method = 'POST', $parameter = array(), $port = 80)
    {
        $url_array = parse_url($request_url);

        $host = $url_array['host'];
        $param = "";
        if (isset($url_array['query'])) {
            $param = $url_array['query'];
        }
        $path = $url_array['path'];
        if ($param != "") {
            $path .= "?" . $param;
        }
        if ($path === "") {
            $path = "/";
        }
        return $this->sendSocket($host, $path, $method, $parameter, $port);
    }

    function sendSocket($host, $path, $method = 'POST', $parameter = array(), $port = 80)
    {
        return $this->_send_socket($host, $port, $this->_makeRequest($host, $path, $method, $parameter));
    }

    function sendPing($host, $path, $method = 'POST', $param = array(), $port = 80)
    {
        return $this->_send_socket($host, $port, $this->_makePingRequest($host, $path, $method, $parameter));
    }
    //-----------------------------
    // 内部関数
    //-----------------------------
    // ソケット送信
    function _send_socket($host, $port, $request)
    {
        $data = '';
        $sock = fsockopen($host, $port);
        if (!$sock) {
            $data = 'socket error：' . $host;
        } else {
            // リクエスト送信
            fputs($sock, $request);
            $p = "";
            // <CRLF>が2つ続くまでループ
            do {
                $p .= fgets($sock);
            } while (strpos($p, "\r\n\r\n") === false);
            // ヘッダーを配列に格納
            $header = $this->decode_header($p);
            $this->header = $header;

            $data = "";
            while (!feof($sock)) {
                $data .= fgets($sock);
            }
            fclose($sock);
            // デコード
            $data = $this->decode_body($header, $data);
        }
        return $data;
    }

    // リクエスト作成
    function _makeRequest($host, $path, $method, $parameter)
    {
        $request = "";
        $request .= $method . " " . $path . " HTTP/1.1\r\n"
            . "Host: " . $host . "\r\n"
            . "User-Agent: PHP/" . phpversion() . "\r\n"
            . "Connection: Close\r\n";
        //POSTの場合
        if ($method == 'POST') {
            $param = array();
            foreach ($parameter as $key => $val) {
                $param[] = $key . '=' . $val;
            }
            $postdata = implode('&', $param);
            $request .= "Content-Type: application/x-www-form-urlencoded\r\n"
                . "Content-Length: " . strlen($postdata) . "\r\n"
                . "\r\n"
                . $postdata;
        } else {
            $request .= "\r\n";
        }
        return $request;
    }

    // PING送信リクエスト作成
    function _makePingRequest($host, $path, $method, $parameter)
    {
        $request = $method . " " . $path . " HTTP/1.1\r\n"
            . "Host: " . $host . "\r\n"
            . "User-Agent: PHP/" . phpversion() . "\r\n"
            . "Connection: Close\r\n";
        //POSTの場合
        if ($method == 'POST') {
            $param = array();
            foreach ($parameter as $key => $val) {
                $param[] = $key . '=' . $val;
            }
            $postdata = implode('&', $param);
            $request .= "Content-Type: text/xml\r\n"
                . "Content-Length: " . strlen($postdata) . "\r\n"
                . "\r\n"
                . $postdata;
        } else {
            $request .= "\r\n";
        }
        return $request;
    }

    // ヘッダーをデコード
    function decode_header($str)
    {
        // <CRLF>ごとに分割
        $part = preg_split("/\r\n/", $str, -1, PREG_SPLIT_NO_EMPTY);
        $out = array();
        for ($h = 0; $h < sizeof($part); $h++) {
            if ($h != 0) {
                // ：で区切ってkeyとvalueを作成
                $pos = strpos($part[$h], ':');
                $k = strtolower(str_replace('', '', substr($part[$h], 0, $pos)));
                $v = trim(substr($part[$h], ($pos + 1)));
            } else {
                // 1行目ステータスコード
                $k = 'status';
                $v = explode(' ', $part[$h]);
                $v = $v[1];
            }
            // keyとvalueを配列に格納
            if ($k == 'set-cookie') {
                $out['cookies'][] = $v;
            } elseif ($k == 'content-type') {
                if (($cs = strpos($v, ';')) !== false) {
                    // 目的が解析なのでサブタイプは切り捨てない//
                    $out[$k] = substr($v, 0, $cs);
                    $out[$k] = $v;
                } else {
                    $out[$k] = $v;
                }
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    function decode_body($info, $str, $eol = "\r\n")
    {
        $tmp = $str;
        $add = strlen($eol);
        $str = '';
        // チャンク形式の判定
        if (isset($info['transfer-encoding']) && $info['transfer-encoding'] == 'chunked') {
            do {
                // チャンクサイズ取得してを10進数に変換
                $tmp = ltrim($tmp);
                $pos = strpos($tmp, $eol);
                $len = hexdec(substr($tmp, 0, $pos));
                // 圧縮転送されている場合解凍する
                if (isset($info['content-encoding'])) {
                    $str .= gzinflate(substr($tmp, ($pos + $add + 10), $len));
                } else {
                    $str .= substr($tmp, ($pos + $add), $len);
                }
                $tmp = substr($tmp, ($len + $pos + $add));
                $check = trim($tmp);
            } while (!empty($check));
        } elseif (isset($info['content-encoding'])) {
            // 圧縮転送されている場合解凍する
            $str = gzinflate(substr($tmp, 10));
        }
        return $str;
    }
}
