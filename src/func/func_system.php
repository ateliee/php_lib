<?php
//============================================
// func_system.php
//============================================
// MIMEタイプと拡張子の対応表
$L_MIME_TYPE = array(
    // テキスト・文書・MSオフィス関連
    array('MIME' => 'text/plain', 'EXT' => 'txt'),
    array('MIME' => 'text/csv', 'EXT' => 'csv'),
    array('MIME' => 'text/tab-separated-values', 'EXT' => 'tsv'),
    array('MIME' => 'application/msword', 'EXT' => 'doc'),
    array('MIME' => 'application/msword', 'EXT' => 'docx'),
    array('MIME' => 'application/vnd.ms-excel', 'EXT' => 'xls'),
    array('MIME' => 'application/vnd.ms-excel', 'EXT' => 'xlsx'),
    array('MIME' => 'application/vnd.ms-powerpoint', 'EXT' => 'ppt'),
    array('MIME' => 'application/pdf', 'EXT' => 'pdf'),
    array('MIME' => 'application/vnd.fujixerox.docuworks', 'EXT' => 'xdw'),
    array('MIME' => 'text/html', 'EXT' => 'htm'),
    array('MIME' => 'text/html', 'EXT' => 'html'),
    array('MIME' => 'text/css', 'EXT' => 'css'),
    array('MIME' => 'text/javascript', 'EXT' => 'js'),
    array('MIME' => 'text/x-hdml', 'EXT' => 'hdml'),
    // 画像関連
    array('MIME' => 'image/jpeg', 'EXT' => 'jpg'),
    array('MIME' => 'image/jpeg', 'EXT' => 'jpeg'),
    array('MIME' => 'image/pjpeg', 'EXT' => 'jpeg'),
    array('MIME' => 'image/png', 'EXT' => 'png'),
    array('MIME' => 'image/gif', 'EXT' => 'gif'),
    array('MIME' => 'image/bmp', 'EXT' => 'bmp'),
    array('MIME' => 'application/postscript', 'EXT' => 'ai'),
    // 音声関連
    array('MIME' => 'audio/mpeg', 'EXT' => 'mp3'),
    array('MIME' => 'audio/mp4', 'EXT' => 'mp4'),
    array('MIME' => 'audio//x-wav', 'EXT' => 'wav'),
    array('MIME' => 'audio/midi', 'EXT' => 'mid'),
    array('MIME' => 'audio/midi', 'EXT' => 'midi'),
    array('MIME' => 'application/x-smaf', 'EXT' => 'mmf'),
    // 動画関連
    array('MIME' => 'video/mpeg', 'EXT' => 'mpg'),
    array('MIME' => 'video/mpeg', 'EXT' => 'mpeg'),
    array('MIME' => 'video/x-ms-wmv', 'EXT' => 'wmv'),
    array('MIME' => 'application/x-shockwave-flash', 'EXT' => 'swf'),
    array('MIME' => 'video/3gpp2', 'EXT' => '3g2'),
    // アプリケーション関連
    array('MIME' => 'application/zip', 'EXT' => 'zip'),
    array('MIME' => 'application/x-lzh', 'EXT' => 'lha'),
    array('MIME' => 'application/x-lzh', 'EXT' => 'lzh'),
    array('MIME' => 'application/x-tar', 'EXT' => 'tar'),
    array('MIME' => 'application/x-tar', 'EXT' => 'tgz'),
    // その他
    array('MIME' => 'application/octet-stream', 'EXT' => 'tar'),
    array('MIME' => 'application/octet-stream', 'EXT' => 'tgz'),
);

//--------------------------------------------
// 文字関係
//--------------------------------------------
//+++++++++++++++++++++++++++++
// 文字コード変換機能付関数
//+++++++++++++++++++++++++++++
function is_dirE($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding($filename, $php_charset, $G_SYSTEM_SERVER_ENCODE);
    return is_dir($filename);
}

function is_fileE($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding($filename, $php_charset, $G_SYSTEM_SERVER_ENCODE);
    return is_file($filename);
}

function file_existsE($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding($filename, $G_SYSTEM_SERVER_ENCODE, $php_charset);
    return file_exists($filename);
}

function unlinkE($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding($filename, $G_SYSTEM_SERVER_ENCODE, $php_charset);
    return unlink($filename);
}

function getimagesizeE($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding($filename, $G_SYSTEM_SERVER_ENCODE, $php_charset);
    return getimagesize($filename);
}

function statE($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding($filename, $G_SYSTEM_SERVER_ENCODE, $php_charset);
    return stat($filename);
}

function mime_content_typeE($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding(realpath($filename), $G_SYSTEM_SERVER_ENCODE, $php_charset);
    // fileinfoリソースを取得
    $finfo = finfo_open(FILEINFO_MIME);
    if ($finfo) {
        // ファイルのMIMEタイプを取得
        $mime_type = finfo_file($finfo, $filename);
        // fileinfoリソースを破棄
        finfo_close($finfo);
        return $mime_type;
    }
    /*
    if(http_get($filename, array("timeout"=>1), $info)){
        return $info['content_type'];
    }
    */
    return false;
// return mime_content_type($filename);
// return f_convertExtentiontoMIME(pathinfo($filename,PATHINFO_EXTENSION));
}

//+++++++++++++++++++++++++++++
// PHPからJavascriptへの文字の受け渡し
//+++++++++++++++++++++++++++++
function convertPHPforJavaScript($str)
{
    return str_replace("\n", '\n', rtrim($str));
}

//+++++++++++++++++++++++++++++
// パスから絶対URLを作成
//+++++++++++++++++++++++++++++
/*
function path_to_url($path, $default_port = 80){
    //ドキュメントルートのパスとURLの作成
    $document_root_url = $_SERVER['SCRIPT_NAME'];
    $document_root_path = $_SERVER['SCRIPT_FILENAME'];
    while(basename($document_root_url) === basename($document_root_path)){
      $document_root_url = dirname($document_root_url);
      $document_root_path = dirname($document_root_path);
    }
    if($document_root_path === '/')  $document_root_path = '';
    if($document_root_url === '/') $document_root_url = '';
    $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] !== 'off')? 'https': 'http';
    $port = ($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != $default_port)? ':'.$_SERVER['SERVER_PORT']: '';
    $document_root_url = $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$document_root_url;
    //絶対パスの取得 (realpath関数ではファイルが存在しない場合や、シンボリックリンクである場合にうまくいかない)
    $absolute_path = realpath($path);
    if(!$absolute_path)
      return false;
    if(substr($absolute_path, -1) !== '/' && substr($path, -1) === '/')
      $absolute_path .= '/';
    //パスを置換して返す
    $url = str_replace($document_root_path, $document_root_url, $absolute_path);
    if($absolute_path === $url)
      return false;
    return $url;
}
*/

//+++++++++++++++++++++++++++++
// バイト数を整形
//+++++++++++++++++++++++++++++
function byte_format($size, $decimals = 0)
{
    $nlist = array(
        "", "K", "M", "G", "T"
    );
    $result = $size;
    foreach ($nlist as $k) {
        if ($size < SYSTEM_BYTE_SIZE) {
            $result = (round($size * pow(10, $decimals)) / pow(10, $decimals)) . $k;
            break;
        }
        $size /= SYSTEM_BYTE_SIZE;
    }
    return $result;
}

//+++++++++++++++++++++++++++++
// 中間の文字列を丸める
//+++++++++++++++++++++++++++++
function strimwidthCenter($str, $length, $trimmarker = "", $encoding = false)
{
    if (!$encoding) {
        $encoding = mb_internal_encoding();
    }
    if ($length >= mb_strwidth($str, $encoding)) {
        return $str;
    }
    $limited = '';
    $firstWidth = ceil($length / 2);
    $secondStart = mb_strwidth($str, $encoding) - ($length - $firstWidth);
    $secondWidth = $length - $firstWidth + 1;
    $limited = mb_strimwidth($str, 0, $firstWidth, $trimmarker, $encoding) . mb_substr($str, $secondStart, $secondWidth, $encoding);
    return $limited;
}

//+++++++++++++++++++++++++++++
// 英数値かチェック
//+++++++++++++++++++++++++++++
function is_Alnum($text)
{
    if (preg_match("/^[a-zA-Z0-9]+$/", $text)) {
        return TRUE;
    }
    return FALSE;
}

//+++++++++++++++++++++++++++++
// 改行を取り除く
//+++++++++++++++++++++++++++++
function spritTrim($text)
{
    return str_replace(array("\r\n", "\n", "\r"), '', $text);
}

//+++++++++++++++++++++++++++++
// 前後の改行を取り除く(マルチバイト版)
//+++++++++++++++++++++++++++++
function mb_trim($str)
{
    return mb_ereg_replace('^[[:space:]]*([\s\S]*?)[[:space:]]*$', '\1', $str);
}

//+++++++++++++++++++++++++++++
// 機種依存文字の置き換え
//+++++++++++++++++++++++++++++
function replace_Dependence_Word($str)
{
    GLOBAL $G_DEPENDENCE_WORD;
    return str_replace(array_keys($G_DEPENDENCE_WORD), array_values($G_DEPENDENCE_WORD), $str);
}

//+++++++++++++++++++++++++++++
// 配列の文字コード変換
//+++++++++++++++++++++++++++++
function recursive_mb_convert_encoding($param, $to_encoding, $from_encoding = "auto")
{
    if (empty($from_encoding)) {
        $from_encoding = "auto";
    }
    if (is_array($param)) {
        foreach ($param as $k => $v) {
            $param[$k] = recursive_mb_convert_encoding($v, $to_encoding, $from_encoding);
        }
    } else {
        $param = mb_convert_encoding($param, $to_encoding, $from_encoding);
    }
    return $param;
}

//+++++++++++++++++++++++++++++
// 配列の最初のキーを取得
//+++++++++++++++++++++++++++++
function firstKey($ary)
{
    if (is_array($ary)) {
        foreach ($ary as $key => $val) {
            return $key;
            break;
        }
    }
    return null;
}

//+++++++++++++++++++++++++++++
// 配列の最後のキーを取得
//+++++++++++++++++++++++++++++
function lastKey($ary)
{
    if (is_array($ary)) {
        $keys = array_keys($ary);
        return $keys[count($keys) - 1];
    }
}

//+++++++++++++++++++++++++++++
// 配列の任意の位置へ要素を挿入
//+++++++++++++++++++++++++++++
function arrayInsert($array, $insert, $pos)
{
    // 引数$arrayが配列でない場合
    if (is_array($array)) {
        // 挿入する位置～末尾まで
        $last = array_splice($array, $pos);
        // 先頭～挿入前位置までの配列に、挿入する値を追加
        array_push($array, $insert);
        // 配列を結合
        $array = array_merge($array, $last);
    }
    return $array;
}

//+++++++++++++++++++++++++++++
// 文字・データのバイト数を取得
//+++++++++++++++++++++++++++++
function getByte($data)
{
    return strlen(bin2hex($data)) / 2;
}

//+++++++++++++++++++++++++++++
// 文字を整形する
//+++++++++++++++++++++++++++++
function reWord($str, $length = null, $trimmarker = '', $default = '')
{
    if ($length > 0) {
        $str = mb_strimwidth($str, 0, $length, $trimmarker);
    }
    // 文字がなければデフォルト指定
    if ($str == "") {
        $str = $default;
    }
    return $str;
}

//+++++++++++++++++++++++++++++
// 文字コードを取得
//+++++++++++++++++++++++++++++
function getEncodingType($data)
{
    $encodingArray = array("ISO-2022-JP", "UTF-8", "Shift_JIS", "EUC", "ASCII");
    $n = 0;
    while ($focusEncoding = $encodingArray[$n]) {
        if (mb_check_encoding($data, $focusEncoding)) {
            return $focusEncoding;
        }
        $n++;
    }
    return "nil";
}

//+++++++++++++++++++++++++++++
// str_pad(マルチバイト版)
//+++++++++++++++++++++++++++++
function mb_str_pad($input, $pad_length, $pad_string = " ", $pad_type = STR_PAD_RIGHT, $encoding = NULL)
{
    if (!$encoding) {
        $encoding = mb_internal_encoding();
    }
    return mb_convert_encoding(str_pad(mb_convert_encoding($input, 'EUC-JP', $encoding), $pad_length, $pad_string, $pad_type), $encoding, 'EUC-JP');
}

//+++++++++++++++++++++++++++++
// カナ文字変換
//+++++++++++++++++++++++++++++
function convertKana($str, $kana)
{
    return mb_convert_kana($str, $kana, 'auto');
}

//+++++++++++++++++++++++++++++
// ユニークなIDを生成する
//+++++++++++++++++++++++++++++
function makeUniqId($num)
{
    $id = "";
    for ($i = 0; $i < $num; $i++) {
        $characters = array(
            1 => 'abcdefghijklmnopqrstuvwxyz',
            2 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            3 => '0123456789'
        );
        $characters_num = rand(1, count($characters));
        $chara = $characters[$characters_num];
        $n = rand(1, strlen($chara)) - 1;
        $id .= substr($chara, $n, 1);
    }
    return $id;
}

//--------------------------------------------
// 変数をチェックして返す
//--------------------------------------------
function evaNumeric($val, $default = 0)
{
    if (isset($val) == false) {
        if (is_numeric($val) == false) {
            $val = $default;
        }
    }
    return $val;
}

function evaValue($val, $default = "")
{
    if (isset($val) == false) {
        $val = $default;
    }
    return $val;
}

function evaArray($val, $array, $default = "")
{
    if (isset($array[$val])) {
        return $array[$val];
    }
    return $default;
}

//--------------------------------------------
// 画像のサイズをリサイズ
//--------------------------------------------
function resizeImageSize($owidth, $oheight, $max_width = 0, $max_height = 0, $scale = 1.0)
{
    $width = $owidth;
    $height = $oheight;
    // サイズ変換
    if ($scale != 1.0) {
        $width *= $scale;
        $height *= $scale;
    }
    if ($max_width > 0 && $width > $max_width) {
        $rate = $max_width / $width;
        $width = $max_width;
        $height = (int)($height * $rate);
    }
    if ($max_height > 0 && $height > $max_height) {
        $rate = $max_height / $height;
        $width = (int)($width * $rate);
        $height = $max_height;
    }
    $rate = 1.0;
    if ($owidth > 0) {
        $rate = ($owidth / $width);
    }
    return array($width, $height, $rate);
}

//--------------------------------------------
// 日付関係
//--------------------------------------------
//+++++++++++++++++++++++++++++
// 時間のフォーマット
//+++++++++++++++++++++++++++++
function time_format($timestamp)
{
    $timestamp -= (($y = floor($timestamp / (3600 * 24 * 30 * 12))) * (3600 * 24 * 30 * 12));
    $timestamp -= (($m = floor($timestamp / (3600 * 24 * 30))) * (3600 * 24 * 30));
    $timestamp -= (($d = floor($timestamp / (3600 * 24))) * (3600 * 24));
    $timestamp -= (($h = floor($timestamp / (3600))) * (3600));
    $timestamp -= (($i = floor($timestamp / (60))) * (60));
    $timestamp -= ($s = floor($timestamp));
    $str = "";
    if ($y > 0) {
        $str .= $y . "年";
    }
    if ($m > 0) {
        $str .= $m . "ヶ月";
    }
    if ($d > 0) {
        $str .= $d . "日";
    }
    if ($h > 0) {
        $str .= $h . "時間";
    }
    if ($i > 0) {
        $str .= $i . "分";
    }
    if ($s > 0) {
        $str .= $s . "秒";
    }
    return $str;
}

//+++++++++++++++++++++++++++++
// 日付用数値取得
//+++++++++++++++++++++++++++++
function getTime($year = 0, $mon = 0, $day = 0, $hour = 0, $minute = 0, $second = 0)
{
    $date = 0;
    $date += $second;
    $date += $minute * 60;
    $date += $hour * 60 * 60;
    $date += $day * 24 * 60 * 60;
    $date += $mon * 30 * 24 * 60 * 60;
    $date += $year * 12 * 30 * 24 * 60 * 60;
    return $date;
}

/*
* 現在の時間を、マイクロ秒単位で取得
* PHP4, 5 互換
* PHP5 以上なら、 microtime(true); で同じ結果が取得できる
*/
function getMicrotime()
{
    list($msec, $sec) = explode(" ", microtime());
    return ((float)$sec + (float)$msec);
}

//--------------------------------------------
// サーバー関係
//--------------------------------------------
//+++++++++++++++++++++++++++++
// 拡張子からMIMEタイプを取得
//+++++++++++++++++++++++++++++
function convertMIMEtoExtention($type)
{
    GLOBAL $L_MIME_TYPE;
    foreach ($L_MIME_TYPE as $value) {
        if ($value['MIME'] == $type) return $value['EXT'];
    }
    return '';
}

function convertExtentiontoMIME($type)
{
    GLOBAL $L_MIME_TYPE;
    foreach ($L_MIME_TYPE as $value) {
        if ($value['EXT'] == $type) return $value['MIME'];
    }
    return '';
}

//+++++++++++++++++++++++++++++
// MIMEをヘッダーに送信
//+++++++++++++++++++++++++++++
function setMIMEHeader($type, $filename, $size = NULL)
{
    $size = (int)$size;
    if (empty($type) == false) {
        header("Content-type: " . $type);
        if (isset($size) && $size > 0) {
            header('Content-Length: ' . $size);
        }
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        return true;
    }
    return false;
}

//+++++++++++++++++++++++++++++
// パーミッションを取得
//+++++++++++++++++++++++++++++
function getPerms($filename)
{
    $perms = fileperms($filename);
    if (($perms & 0xC000) == 0xC000) {
        // ソケット
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // シンボリックリンク
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // 通常のファイル
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // ブロックスペシャルファイル
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // ディレクトリ
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // キャラクタスペシャルファイル
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO パイプ
        $info = 'p';
    } else {
        // 不明
        $info = 'u';
    }

    // 所有者
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
        (($perms & 0x0800) ? 's' : 'x') :
        (($perms & 0x0800) ? 'S' : '-'));
    // グループ
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
        (($perms & 0x0400) ? 's' : 'x') :
        (($perms & 0x0400) ? 'S' : '-'));
    // 全体
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
        (($perms & 0x0200) ? 't' : 'x') :
        (($perms & 0x0200) ? 'T' : '-'));
    return $info;
}

//+++++++++++++++++++++++++++++
// file_get_contentsの拡張版
//+++++++++++++++++++++++++++++
function file_get_contentsEX($server, $timeout = 10)
{
    $data = '';
    $fp = @fsockopen($server, 80, $errno, $errstr, $timeout);
    if ($fp) {
        while ($eof_check = @fread($fp, 1024)) {
            $data .= $eof_check;
        }
        @fclose($fp);
    } else {
        return false;
    }
    return $data;
}

//+++++++++++++++++++++++++++++
// HTTPステータスエラー
//+++++++++++++++++++++++++++++
function httpError($code)
{
    switch ($code) {
        //=======================================
        // クライアントエラー(400)
        //=======================================
        // 401エラー(ユーザの認証に失敗)
        case "401":
            header('HTTP/1.0 401 Unauthorized');
            // header('HTTP/1.1 401 Authorization Required');
            die('401 Unauthorized');
            exit;
            break;
        // 403エラー(アクセスが許可されていない・アクセス権限がないページのリクエスト時)
        case "403":
            header('HTTP/1.0 403 Forbidden');
            die('403 Forbidden');
            break;
        // 404エラー(要求されたリソースがサーバに存在していない)
        case "404":
            header("HTTP/1.1 404 Not Found");
            die('404 Not Found');
            exit;
            break;
        //=======================================
        // サーバーエラー(500)
        //=======================================
        // 500エラー(予期しない理由でサーバがリクエストを実行できない・ＣＧＩエラー時)
        case "500":
            header('HTTP/1.0 401 Internal Server Error');
            exit;
            break;
        // 503エラー(過負荷などでリクエストが実行できない・メンテナンス中などに表示)
        case "503":
            header('HTTP/1.0 503 Service Unavailable');
            exit;
            break;
    }
    return false;
}

//--------------------------------------------
// ファイル関係
//--------------------------------------------
//+++++++++++++++++++++++++++++
// 外部ファイルが存在するか調べる
//      $url     : http://から始まるURL( http://user:pass@host:port/path?query )
//+++++++++++++++++++++++++++++
function url_exists($url, $userpwd = "")
{
// $header = get_headers($url);
// if(strstr($header[0], '200')) return true;
// return false;
    $url_parse = parse_url($url);
    // Version 4.x supported
    $handle = curl_init($url);
    if (false === $handle) {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true); // this works
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent:" . $_SERVER['HTTP_USER_AGENT'])); // request as if Firefox
    curl_setopt($handle, CURLOPT_NOBODY, true);
    if (isset($url_parse['user']) && isset($url_parse['pass'])) {
        curl_setopt($handle, CURLOPT_USERPWD, base64_encode($url_parse['user'] . ":" . $url_parse['pass']));
    }
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);
    return $connectable;
}

//+++++++++++++++++++++++++++++
// 外部ファイルを取得
//+++++++++++++++++++++++++++++
function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    ob_start();
    curl_exec($ch);
    curl_close($ch);
    $string = ob_get_contents();
    ob_end_clean();

    return $string;
}

//+++++++++++++++++++++++++++++
// リクエストを送る
//+++++++++++++++++++++++++++++
// file_get_contents版
function requestParams_fileGetContents($method, $url, $params = array(), $headers = array())
{
    if (count($headers) <= 0) {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            //'User-Agent: My User Agent 1.0',    // ユーザエージェントの指定
            //'Authorization: Basic '.base64_encode('user:pass'),// ベーシック認証
        );
    }
    $data = http_build_query($params);
    $options = array(
        'http' => array(
            'method' => $method,
            'header' => implode("\r\n", $headers),
        )
    );
    // ステータスをチェック / PHP5専用 get_headers()
    if (phpmajorversion() >= 5) {
        $respons = get_headers($url);
        if (preg_match("/(404|403|500)/", $respons['0'])) {
            return false;
        }
    }
    if ($method == 'GET') {
        $url = ($data != '') ? $url . '?' . $data : $url;
    } else if ($method == 'POST') {
        $options['http']['content'] = $data;
    }
    $contents = file_get_contents($url, false, stream_context_create($options));
    return $contents;
}

// fsockopen版
function requestParams_fsock($method, $url, $params = array(), $headers = array())
{
    // URLをパース
    $url_parse = parse_url($url);
    // 送信するデータを生成
    $request = http_build_query($params);

    $response = "";
    if ($fp = fsockopen($url_parse['host'], 80)) {
        fputs($fp, $method . " " . $url_parse['path'] . " HTTP/1.1\r\n");
        fputs($fp, "User-Agent:PHP/" . phpversion() . "\r\n");
        fputs($fp, "Host: " . $_SERVER["HTTP_HOST"] . "\r\n");
        fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-Length: " . strlen($request) . "\r\n\r\n");
        fputs($fp, $request);
        while (!feof($fp)) {
            $response .= fgets($fp, 4096);
        }
        fclose($fp);
    }
    return $response;
}

// curl版
function requestParams_curl($method, $url, $params = array(), $headers = array())
{
    // URLをパース
    $url_parse = parse_url($url);
    // 送信するデータを生成
    $request = http_build_query($params);

    // curlで送信
    $ch = curl_init($url);
    if (false === $ch) {
        return false;
    }
    if (strtoupper($method) == "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($request != "") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
    } else {
        curl_setopt($ch, CURLOPT_URL, $url . ($request != "" ? "?" . $request : ""));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }
    // ヘッダを画面出力しない
    curl_setopt($ch, CURLOPT_HEADER, false);
    // Locationが指定されていればその先を呼び出す
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Locationを辿る最大回数
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    //400以上のステータスコードが返ってきた場合取得しない
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    if (isset($url_parse['user']) && isset($url_parse['pass'])) {
        curl_setopt($ch, CURLOPT_USERPWD, base64_encode($url_parse['user'] . ":" . $url_parse['pass']));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

//+++++++++++++++++++++++++++++
// MIMEタイプを分割
//+++++++++++++++++++++++++++++
function getMIME($mime)
{
    $mime_type = array();
    $key = preg_split("/\//", $mime);
    $mime_type['TYPE'] = $key[0];
    $mime_type['NAME'] = $key[1];
    return $mime_type;
}

//+++++++++++++++++++++++++++++
// 画像データからファイルタイプを取得
//+++++++++++++++++++++++++++++
function getImageTypeForStream($image_stream)
{
    if (preg_match('/^\x89PNG\x0d\x0a\x1a\x0a/', $image_stream)) {
        $type = "png";
    } elseif (preg_match('/^GIF8[79]a/', $image_stream)) {
        $type = "gif";
    } elseif (preg_match('/^\xff\xd8/', $image_stream)) {
        $type = "jpg";
    }
    return $type;
}

//+++++++++++++++++++++++++++++
// ファイルサイズを文字列に変換
//+++++++++++++++++++++++++++++
function getFileSizeString($pattern, $size)
{
    $b = $size;
    $k = round($b / 1024, 0);
    $m = round($k / 1024, 0);
    $g = round($m / 1024, 0);
    $str = $pattern;
    $str = preg_replace('/\\\B/', $b, $str);
    $str = preg_replace('/\\\K/', $k, $str);
    $str = preg_replace('/\\\M/', $m, $str);
    $str = preg_replace('/\\\G/', $g, $str);
    return $str;
}

//+++++++++++++++++++++++++++++
// ディレクトリの削除
//+++++++++++++++++++++++++++++
function deleteDir($filename, $delete = true)
{
    $strDir = opendir($filename);
    while ($strFile = readdir($strDir)) {
        if ($strFile != '.' && $strFile != '..') { //ディレクトリでない場合のみ
            $path = $filename . "/" . $strFile;
            if (is_dir($path)) {
                deleteDir($path);
            } else {
                @unlink($path);
            }
        }
    }
    if ($delete) {
        @rmdir($filename);
    }
}

//+++++++++++++++++++++++++++++
// ディレクトリの一覧を取得
//+++++++++++++++++++++++++++++
function getDir($filename)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $filename = mb_convert_encoding($filename, $G_SYSTEM_SERVER_ENCODE, $php_charset);

    $aut = array();
    if (!(is_dir($filename))) return NULL;
    if (!($dp = opendir($filename))) return NULL;
    while (false !== ($file = readdir($dp))) {
        if ($file != "." && $file != "..") {
            $file = mb_convert_encoding($file, $php_charset, $G_SYSTEM_SERVER_ENCODE);
            $aut[] = $file;
        }
    }
    closedir($dp);
    return $aut;
}

//+++++++++++++++++++++++++++++
// ファイルの権限を取得
//+++++++++++++++++++++++++++++
function getAuthority($filename)
{
    $aut = array();
    $aut['read'] = is_readable($filename); // 読み取り可能
    $aut['write'] = is_writable($filename); // 書き込み可能
    $aut['execut'] = is_executable($filename); // 実行可能
    return $aut;
}

//+++++++++++++++++++++++++++++
// ファイルのパーミッションの変更
//+++++++++++++++++++++++++++++
function fileChmod($src, $dest)
{
    if (file_exists($src)) {
        if (copy($src, $dest)) {
            chmod($dest, 0777);
            return true;
        }
    }
    return false;
}

//+++++++++++++++++++++++++++++
// ファイルのコピー
//+++++++++++++++++++++++++++++
function fileCopy($src, $dest)
{
    GLOBAL $G_SYSTEM_SERVER_ENCODE;
    $php_charset = mb_internal_encoding();
    $dest = mb_convert_encoding($dest, $G_SYSTEM_SERVER_ENCODE, $php_charset);
    $src = mb_convert_encoding($src, $G_SYSTEM_SERVER_ENCODE, $php_charset);
    if (file_exists($src)) {
        if (copy($src, $dest)) {
            return true;
        }
    }
    return false;
}

//+++++++++++++++++++++++++++++
// ファイルの作成
//+++++++++++++++++++++++++++++
function fileTouch($filename, $time = NULL)
{
    if (file_exists($filename)) {
        if (touch($filename, $time)) {
            return true;
        }
    }
    return false;
}

//+++++++++++++++++++++++++++++
// ファイルを書き込む
//+++++++++++++++++++++++++++++
function writeFile($filename, $data = NULL, $prm = 0666)
{
    // ファイルオープン
    if (!($fp = fopen($filename, 'w+'))) {
        return false;
    }
    if ($data) {
        $charset = mb_internal_encoding();
        $data = mb_convert_encoding($data, $charset, "auto");
        flock($fp, LOCK_EX); //ファイルをロックします
        fputs($fp, $data); //書込みです
        flock($fp, LOCK_UN); //ロック解除
    }
    // ファイルを閉じる
    fclose($fp);
    // パーミッション変更
    chmod($filename, $prm);
    return true;
}

//+++++++++++++++++++++++++++++
// 連想配列表示
//+++++++++++++++++++++++++++++
function showValue($args)
{
    echo '<pre>';
    print_r($args);
    echo '</pre>';
}

//+++++++++++++++++++++++++++++
// ファイルサイズを取得
//+++++++++++++++++++++++++++++
function getFileSize($path)
{
    $total_size = 0;
    //指定したのがファイルだった場合はサイズを返して終了。
    if (is_file($path)) {
        return filesize($path);
    } elseif (is_dir($path)) {
        $basename = basename($path);
        //カレントディレクトリと上位ディレクトリを指している場合はここで終了。
        if ($basename == '.' || $basename == '..') {
            return 0;
        }
        //ディレクトリ内のファイル一覧を入手。
        $file_list = scandir($path);
        foreach ($file_list as $file) {
            //ディレクトリ内の各ファイルを引数にして、自分自身を呼び出す。
            $total_size += getFileSize($path . '/' . $file);
        }
        return $total_size;

    }
    return 0;
}

$GETREMOTELASTMOD_RESULT = 0;
function read_header($ch, $header)
{
    GLOBAL $GETREMOTELASTMOD_RESULT;
    $length = strlen($header);
    if (strstr($header, "Last-Modified:")) {
        $GETREMOTELASTMOD_RESULT = strtotime(substr($header, 15));
    }
    return $length;
}

function GetRemoteLastModified($remote_file)
{
    GLOBAL $GETREMOTELASTMOD_RESULT;
    $GETREMOTELASTMOD_RESULT = 0;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_file);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'read_header');

    $headers = curl_exec($ch);
    curl_close($ch);
    return $GETREMOTELASTMOD_RESULT;
}

//--------------------------------------------
// メール関係
//--------------------------------------------
//+++++++++++++++++++++++++++++
// URLか調べる
//+++++++++++++++++++++++++++++
function checkURL($url)
{
    $preg_str = '/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/';
    return preg_match($preg_str, $url);
}

//+++++++++++++++++++++++++++++
// メールアドレスか調べる
//+++++++++++++++++++++++++++++
function checkMail($mail)
{
    $preg_str = "/^([a-zA-Z0-9])+([a-zA-Z0-9\\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\\._-]+)+$/";
    return preg_match($preg_str, $mail);
}

//+++++++++++++++++++++++++++++
// メールを送信する
//+++++++++++++++++++++++++++++
/*
function f_send_mail($to,$subject,$body,$from,$temp=null,$encode=''){
      // ini_set( "SMTP", "localhost" );
      // ini_set( "smtp_port", "25" );
      $php_charset = mb_internal_encoding();
      if(empty($encode)){
            $encode = 'JIS';
      }
      
      $from = "From:".$from;
      //メール送信
      $to = $to;
      $subject = mb_encode_mimeheader(mb_convert_encoding($subject,$encode,$php_charset));
      $body = mb_encode_mimeheader(mb_convert_encoding($body,$encode,$php_charset));
      $from = mb_encode_mimeheader(mb_convert_encoding($from,$encode,$php_charset));
      
      // メールを送信する
      if(empty($temp)){
            return mb_send_mail($to,$subject,$body,$from); 
      }
      // 添付ファイルがある場合
      $boundary = md5(uniqid(rand())); //バウンダリー文字(パートの境界)
      $header = "From: ".$from."\n";
      $header .= "Reply-To: ".$from."\n";
      $header .= "X-Mailer: PHP/".phpversion()."\n";
      $header .= "MIME-version: 1.0\n";
      $header .= "Content-Type: multipart/mixed;\n";
      $header .= "\tboundary=\"".$boundary."\"\n";
      $msg = "";
      $msg .= "This is a multi-part message in MIME format.\n\n";
      $msg .= "--".$boundary."\n";
      $msg .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
      $msg .= "Content-Transfer-Encoding: 7bit\n\n";
      $msg .= $body;
      // ファイルが存在するか調べる
      if(file_exists($temp)){
            $fp = fopen($temp, "r") or die("error"); //ファイルの読み込み
            $file = fread($fp, filesize($temp));
            fclose($fp);
            //エンコードして分割
            $file_encoded = chunk_split(base64_encode($file));
            $msg .= "\n\n--".$boundary."\n";
            $msg .= "Content-Type: " . $upfile_type . ";\n";
            $msg .= "\tname=\"".$upfile_name."\"\n";
            $msg .= "Content-Transfer-Encoding: base64\n";
            $msg .= "Content-Disposition: attachment;\n";
            $msg .= "\tfilename=\"".$upfile_name."\"\n\n";
            $msg .= $file_encoded."\n";
            $msg .= "--".$boundary."--";
      }
      return mail($to, $subject, $msg, $header);
}
*/

/**
 * linux コマンド実行
 *
 * @param $command
 * @param $output
 * @return string
 */
function linuxCommand($command, $output)
{
    return exec($command . " 2>&1", $output);
}

/**
 * PHPinfoを文字列で取得
 *
 * @return mixed|string
 */
function phpinfo_str()
{
    // Get PHP INFO
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    $phpinfo = preg_replace('/<\/div><\/body><\/html>/', '', $phpinfo);

    //HR
    $hr = '<div style="width:100%;background:#000; height:10px;margin-bottom:1em;"></div>' . PHP_EOL;

    //GET EXT INFO
    ob_start();
    echo '<table border="0" cellpadding="3">' . PHP_EOL;
    echo '<tr class="h"><td><a href="http://www.php.net/">';
    echo '<img border="0" src="http://static.php.net/www.php.net/images/php.gif" alt="PHP Logo" />';
    echo '</a><h1 class="p">PHP Extensions</h1>' . PHP_EOL;
    echo '</td></tr>' . PHP_EOL;
    echo '</table><br />' . PHP_EOL;
    echo '<h2>Overview</h2>' . PHP_EOL;
    echo '<table border="0" cellpadding="3">' . PHP_EOL;
    echo '<tr><td class="e">Extensions</td><td class="v">' . PHP_EOL;
    $exts = array();
    foreach (get_loaded_extensions() as $ext) {
        $exts[] = $ext;
    }
    echo implode(', ', $exts) . PHP_EOL;
    echo '</td></tr></table><br />' . PHP_EOL;
    echo '<h2>Details</h2>' . PHP_EOL;
    echo '<table border="0" cellpadding="3">' . PHP_EOL;
    foreach ($exts as $ext) {
        echo '<tr><td class="e">' . $ext . '</td><td class="v">';
        $funcs = array();
        foreach (get_extension_funcs($ext) as $func) {
            $funcs[] = $func;
        }
        echo implode(', ', $funcs) . PHP_EOL;
        echo '</td></tr>' . PHP_EOL;
    }
    echo '</table><br />' . PHP_EOL;
    echo '</div></body></html>' . PHP_EOL;
    $extinfo = ob_get_contents();
    ob_end_clean();

    $p = $phpinfo . $hr . $extinfo;

    $p = preg_replace('/<\!DOCTYPE(.*?)">/', '', $p);
    $p = preg_replace('/<head>([\s\S]*?)<\/head>/', '', $p);
    $p = preg_replace('/<html>([\s\S]*)?<\/html>/', '$1', $p);
    $p = preg_replace('/<body>([\s\S]*)?<\/body>/', '$1', $p);
    $p = preg_replace('/(<table )([\s\S]*?)(width="[0-9]+")([\s\S]*?)(>)/', '$1$2$4$5', $p);
    //OUTPUT
    return $p;
}

/**
 * リサイズ
 *
 * @param $width
 * @param $height
 * @param int $max_width
 * @param int $max_height
 * @return array
 */
function resizeCalculation($width, $height, $max_width = 0, $max_height = 0)
{
    $rate_w = $max_width / $width;
    $rate_h = $max_height / $height;
    if ($rate_w < 1 && ($rate_w < $rate_h)) {
        $width = $max_width;
        $height = floor($height * $rate_w);
    } elseif ($rate_h < 1 && ($rate_w > $rate_h)) {
        $height = $max_height;
        $width = floor($width * $rate_h);
    }
    return array($width, $height);
}

/**
 * get vendor dir
 */
function getVendorDir($filename='',$vendor_dir = 'vendor'){
    $dirs = explode(DIRECTORY_SEPARATOR,dirname(__FILE__));
    while(count($dirs) > 0){
        $dir = implode(DIRECTORY_SEPARATOR,$dirs).'/'.$vendor_dir;
        if(file_exists($dir) && is_dir($dir)){
            return $dir;
        }
        array_pop($dirs);
    }
    return false;
}