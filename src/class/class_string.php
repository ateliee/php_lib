<?php
//============================================
// class_string.php
//============================================

//+++++++++++++++++++++++++++++
// 文字列クラス
//+++++++++++++++++++++++++++++
class class_string
{
    // HTMLの文字列をエスケープ
    function htmlEscape($str)
    {
        return htmlspecialchars($str);
    }

    function htmlEscapeArray($ary)
    {
        $tmp = $ary;
        if (is_array($tmp)) {
            foreach ($tmp as $key => $val) {
                $tmp[$key] = $this->htmlEscapeArray($val);
            }
        } else {
            $tmp = $this->htmlEscape($tmp);
        }
        return $tmp;
    }

    // HTMLおよびPHPタグの取り除き
    function stripTags($str, $allowable_tags = '')
    {
        return strip_tags($str, $allowable_tags);
    }

    // URLをエンコード
    function urlEncode($str)
    {
        return urlencode($str);
    }

    function urlDecode($str)
    {
        return urldecode($str);
    }

    // 正規表現のエスケープ
    function pregEncode($str)
    {
        return preg_quote($str, "/");
    }

    // 文字を変換する
    function convertKana($str, $option, $encoding = 'auto')
    {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = $this->convertKana($val, $option, $encoding);
            }
        } else {
            return mb_convert_kana($str, $option, $encoding);
        }
        return $str;
    }

    // 文字コードを変換する
    function convertEncode($str, $to_encoding, $from_encoding = 'auto')
    {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = $this->convertEncode($val, $to_encoding, $from_encoding);
            }
        } else {
            return mb_convert_encoding($str, $to_encoding, $from_encoding);
        }
        return $str;
    }

    // 文字列を単語で分解
    function parsingWord($str)
    {
        if (preg_match("/\B/", $str, $words)) {
            return $words;
        }
    }
}

// $str = array_map('trim', $arr);
/*
$charset = $_REQUEST['charset'];
mb_http_output($charset);
ob_start('mb_output_handler');
header("Content-Type: text/plain;charset=".$charset);
*/
/*//サンプル
function fget($f)      { return( file_get_contents(fencode($f))      ); }
function fput($f,$s)      { return( file_put_contents(fencode($f),$s)      ); }
function ftime($f)      { return( filemtime(fencode($f))            ); }
function fsize($f)      { return( filesize(fencode($f))                  ); }
function fencode($s)      { return( mb_conv($s,"php>file")            ); }
function fdecode($s)      { return( mb_conv($s,"file>php")            ); }
function hencode($s)      { return( mb_conv($s,"php>http")            ); }
function hdecode($s)      { return( mb_conv($s,"http>php")            ); }
function sencode($s)      { return( mb_conv($s,"php>swf")                  ); }
function sdecode($s)      { return( mb_conv($s,"swf>php")                  ); }

function mb_conv($s,$from_to){
      //$enc = $GLOBALS["ini"]["enc"];
      $enc = array(
            "file"      => "SJIS-win",      //Winの場合。サーバー上ならFTP転送エンコード、
                              //OSXの場合は Unicode ローカルフォーマット
            "http"      => "SJIS",      //xampp なら UTF-8
            "php"      => "UTF-8",
            "swf"      => "UTF-8",
      );
      $trans = explode(">",strtolower($from_to));
      for($i=0;$i<count($trans)-1;$i++){
            $s = mb_convert_encoding( $s, $enc[$trans[$i+1]], $enc[$trans[$i]] ) );
      } 
      return($s); 
}
*/
