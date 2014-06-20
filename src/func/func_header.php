<?php
//============================================
// func_header.php
//============================================
//+++++++++++++++++++++++++++++
// BASIC認証
//+++++++++++++++++++++++++++++
function AuthenticateUser($auth_list, $realm = "Restricted Area", $failed_text = "認証に失敗しました")
{
    if (isset($_SERVER['PHP_AUTH_USER']) and isset($auth_list[$_SERVER['PHP_AUTH_USER']])) {
        if (isset($auth_list[$_SERVER['PHP_AUTH_USER']])) {
            if ($auth_list[$_SERVER['PHP_AUTH_USER']] == $_SERVER['PHP_AUTH_PW']) {
                return $_SERVER['PHP_AUTH_USER'];
            }
        }
    }
    header('WWW-Authenticate: Basic realm="' . $realm . '"');
    header('HTTP/1.0 401 Unauthorized');
    header('Content-type: text/html; charset=' . mb_internal_encoding());
    die($failed_text);
}

//--------------------------------------------
// ヘッダーを表示
//--------------------------------------------
function f_getHeaders()
{
    /*
          $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
          if($_SESSION['GET_HEADER'] == 1){
                unset($_SESSION['GET_HEADER']);
                return NULL;
          }else{
                $_SESSION['GET_HEADER'] = 1;
          }
          // if($_SERVER['HTTP_REFERER'] === $url){
          $headers = @get_headers($url,1);
          $str = '';

          $str .= '<table cellspacing="1" summary="ヘッダー">'."\n";
          $str .= '<tr><th>REQUUEST</th><td>'.$url.'</td></tr>'."\n";
          $str .= '<tr>'."\n";
          foreach($headers as $key => $value){
                $str .= '<th>'.$key.'</th>'.'<td>'.$value.'</td>'."\n";
                $str .= '</tr>'."\n";
          }
          $str .= '</table>'."\n";
          return $str;
          */
    $str = "";
    return $str;
}

//-------------------------------------------------------------------------
// array get_http_header( string URI )
// URIがHTTPプロトコルだった場合、そのURIにHEADリクエストを行います。
// 返り値にはHTTP-Version、Status-Code、Reason-Phraseが必ず含まれ、それ以外
// にサーバが返した情報（index: value）が含まれます。
// Status-Codeが9xxの場合、それはホストが存在しない場合などHTTPリクエストが
// 正常に行われなかったことを意味します。
//-------------------------------------------------------------------------
function get_http_header($target)
{
    // URIから各情報を取得
    $info = parse_url($target);

    $scheme = $info['scheme'];
    $host = $info['host'];
    $port = $info['port'];
    $path = $info['path'];
    // ポートが空の時はデフォルトの80にします。
    if (!$port) {
        $port = 80;
    }
    // リクエストフィールドを制作。
    $msg_req = "HEAD " . $path . " HTTP/1.0\r\n";
    $msg_req .= "Host: " . $host . "\r\n";
    $msg_req .= "User-Agent: H2C/1.0\r\n";
    $msg_req .= "\r\n";

    // スキームがHTTPの時のみ実行
    if ($scheme == 'http') {
        $status = array();

        // 指定ホストに接続。
        if ($handle = @fsockopen($host, $port, $errno, $errstr, 1)) {
            fputs($handle, $msg_req);
            if (socket_set_timeout($handle, 3)) {
                $line = 0;
                while (!feof($handle)) {
                    // 1行めはステータスライン
                    if ($line == 0) {
                        $temp_stat = explode(' ', fgets($handle, 4096));
                        $status['HTTP-Version'] = array_shift($temp_stat);
                        $status['Status-Code'] = array_shift($temp_stat);
                        $status['Reason-Phrase'] = implode(' ', $temp_stat);
                        // 2行目以降はコロンで分割してそれぞれ代入
                    } else {
                        $temp_stat = explode(':', fgets($handle, 4096));
                        $name = array_shift($temp_stat);
                        // 通常:の後に1文字半角スペースがあるので除去
                        $status[$name] = substr(implode(':', $temp_stat), 1);
                    }
                    $line++;
                }
            } else {
                $status['HTTP-Version'] = '---';
                $status['Status-Code'] = '902';
                $status['Reason-Phrase'] = "No Response";
            }
            fclose($handle);

        } else {
            $status['HTTP-Version'] = '---';
            $status['Status-Code'] = '901';
            $status['Reason-Phrase'] = "Unable To Connect";
        }
    } else {
        $status['HTTP-Version'] = '---';
        $status['Status-Code'] = '903';
        $status['Reason-Phrase'] = "Not HTTP Request";
    }
    return $status;

}

?>