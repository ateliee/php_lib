<?php
//============================================
// func_php.php
//  upgrade.php(http://upgradephp.berlios.de/)を参考
//============================================

//+++++++++++++++++++++++++++++
// PHPのバージョンを取得
//+++++++++++++++++++++++++++++
function phpmajorversion(){
      $va = explode('.',PHP_VERSION);
      return $va[0];
}
function phpminorversion(){
      $va = explode('.',PHP_VERSION);
      return $va[1];
}

//+++++++++++++++++++++++++++++
// 短縮関数
//+++++++++++++++++++++++++++++
// 配列の内容をキレイに表示する関数
function d(){
        print '<pre style="background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px">';
        foreach (func_get_args() as $v){
             var_dump($v);
        }
        print '</pre>';
}
// htmlのエスケープ
function h($str){
    if(is_array($str)){
        return array_map( "h",$str );
    }else{
        return htmlspecialchars($str,ENT_QUOTES);
    }
}
// MySQLエスケープ
function e($str){
    if(is_array($str)){
        return array_map( "e" , $str );
    }else{
        return mysql_real_escape_string( $str );
    }
}
function print_backtrace(){
   $backtrace = debug_backtrace();
   echo "<table border=\"1\" cellpadding=\"3\">";
   echo "<tr align=\"center\"><td>#</td><td>call</td><td>path</td></tr>";
   foreach ($backtrace as $key => $val){
       echo "<tr><td>".$key."</td>";
       echo "<td>".$val['function']."(".print_r($val['args'],true).")</td>";
       echo "<td>".$val['file']." on line ".$val['line']."</td></tr>";
   }
   echo "</table>";
}

//+++++++++++++++++++++++++++++
// パス情報を取得する
//+++++++++++++++++++++++++++++
function m_pathinfo($path,$options=NULL){
      if(!$options){
            $options = PATHINFO_DIRNAME + PATHINFO_BASENAME + PATHINFO_EXTENSION;
            if(defined('PATHINFO_FILENAME')){
                  $options += PATHINFO_FILENAME;
            }
      }
      $p = pathinfo($path,$options);
      // PHP 5.2.0 以前
      if(isset($p['filename']) == false){
            if(isset($p['extension']) && ($p['extension'] != "")){
                  $p['filename'] = basename($p['basename'],'.'.$p['extension']);
            }else{
                  $p['filename'] = $p['basename'];
            }
      }
      return $p;
}

//+++++++++++++++++++++++++++++
// UTF-8文字列をUnicodeエスケープする。ただし英数字と記号はエスケープしない。
//+++++++++++++++++++++++++++++
if(!function_exists("unicode_decode")){
        function unicode_decode($str) {
          return preg_replace_callback("/((?:[^\x09\x0A\x0D\x20-\x7E]{3})+)/", "decode_callback", $str);
        }
        function decode_callback($matches) {
          $char = mb_convert_encoding($matches[1], "UTF-16", "UTF-8");
          $escaped = "";
          for ($i = 0, $l = strlen($char); $i < $l; $i += 2) {
            $escaped .=  "\u" . sprintf("%02x%02x", ord($char[$i]), ord($char[$i+1]));
          }
          return $escaped;
        }
}

//+++++++++++++++++++++++++++++
// Unicodeエスケープされた文字列をUTF-8文字列に戻す
//+++++++++++++++++++++++++++++
if(!function_exists("unicode_encode")){
        function unicode_encode($str) {
          return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", "encode_callback", $str);
        }
        function encode_callback($matches) {
          $char = mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
          return $char;
        }
}
//+++++++++++++++++++++++++++++
// scandir
//+++++++++++++++++++++++++++++
if (!function_exists('scandir')) {
      function scandir($dir_name, $order = '') {
            $dh = opendir($dir_name);
            while(($filename = readdir($dh)) !== false) {
                  $file_list[] = $filename;
            }
            if($order){
                  rsort($file_list);
            }else{
                  sort($file_list);
            }
            return $file_list;
      }
}

//+++++++++++++++++++++++++++++
// http_build_query
//+++++++++++++++++++++++++++++
if (!function_exists('http_build_query')) {
      function http_build_query($query_data, $numeric_prefix = '', $arg_separator = '', $key = '') {
            $ret = array();
            foreach ((array)$query_data as $k => $v) {
                  if(is_int($k) && $numeric_prefix != null) {
                        $k = urlencode($numeric_prefix . $k);
                  }
                  if((!empty($key)) || ($key === 0)) {
                        $k = $key . '[' . urlencode($k) . ']';
                  }
                  if(is_array($v) || is_object($v)) {
                        array_push($ret, http_build_query($v, '', $arg_separator, $k));
                  }else{
                        array_push($ret, $k . '=' . urlencode($v));
                  }
            }
            if (empty($arg_separator)) {
                  $arg_separator = ini_get('arg_separator.output');
            }
            return implode($arg_separator, $ret);
      }
}

//+++++++++++++++++++++++++++++
// json_encode
//+++++++++++++++++++++++++++++
if (!function_exists("json_encode")) {
   function json_encode($var, /*emu_args*/$obj=FALSE) {
      #-- prepare JSON string
      $json = "";
      #-- add array entries
      if (is_array($var) || ($obj=is_object($var))) {
         #-- check if array is associative
         if (!$obj) foreach ((array)$var as $i=>$v) {
            if (!is_int($i)) {
               $obj = 1;
               break;
            }
         }
         #-- concat invidual entries
         foreach ((array)$var as $i=>$v) {
            $json .= ($json ? "," : "")    // comma separators
                   . ($obj ? ("\"$i\":") : "")   // assoc prefix
                   . (json_encode($v));    // value
         }
         #-- enclose into braces or brackets
         $json = $obj ? "{".$json."}" : "[".$json."]";
      } elseif (is_string($var)) {
         if (!utf8_decode($var)) {
            $var = utf8_encode($var);
         }
         $var = str_replace(array("\\", "\"", "/", "\b", "\f", "\n", "\r", "\t"), array("\\\\", '\"', "\\/", "\\b", "\\f", "\\n", "\\r", "\\t"), $var);
         $json = '"' . $var . '"';
         //@COMPAT: for fully-fully-compliance   $var = preg_replace("/[\000-\037]/", "", $var);
      } elseif (is_bool($var)) {
         $json = $var ? "true" : "false";
      } elseif ($var === NULL) {
         $json = "null";
      } elseif (is_int($var) || is_float($var)) {
         $json = "$var";
      } else {
         trigger_error("json_encode: don't know what a '" .gettype($var). "' is.", E_USER_ERROR);
      }
      return($json);
   }
}

//+++++++++++++++++++++++++++++
// json_decode
//+++++++++++++++++++++++++++++
if (!function_exists("json_decode")) {
   function json_decode($json, $assoc=FALSE, $limit=512, /*emu_args*/$n=0,$state=0,$waitfor=0) {
      #-- result var
      $val = NULL;
      static $lang_eq = array("true" => TRUE, "false" => FALSE, "null" => NULL);
      static $str_eq = array("n"=>"\012", "r"=>"\015", "\\"=>"\\", '"'=>'"', "f"=>"\f", "b"=>"\b", "t"=>"\t", "/"=>"/");
      if ($limit<0) return /* __cannot_compensate */;
      for (/*n*/; $n<strlen($json); /*n*/) {
         $c = $json[$n];
         #-= in-string
         if ($state==='"') {
            if ($c == '\\') {
               $c = $json[++$n];
               // simple C escapes
               if (isset($str_eq[$c])) {
                  $val .= $str_eq[$c];
               } elseif ($c == "u") {
                  // read just 16bit (therefore value can't be negative)
                  $hex = hexdec( substr($json, $n+1, 4) );
                  $n += 4;
                  // Unicode ranges
                  if ($hex < 0x80) {    // plain ASCII character
                     $val .= chr($hex);
                  } elseif ($hex < 0x800) {   // 110xxxxx 10xxxxxx 
                     $val .= chr(0xC0 + $hex>>6) . chr(0x80 + $hex&63);
                  } elseif ($hex <= 0xFFFF) { // 1110xxxx 10xxxxxx 10xxxxxx 
                     $val .= chr(0xE0 + $hex>>12) . chr(0x80 + ($hex>>6)&63) . chr(0x80 + $hex&63);
                  }
                  // other ranges, like 0x1FFFFF=0xF0, 0x3FFFFFF=0xF8 and 0x7FFFFFFF=0xFC do not apply
               } else {
                  $val .= "\\" . $c;
               }
            } elseif ($c == '"') {
               $state = 0;
            } else/*if (ord($c) >= 32)*/ { //@COMPAT: specialchars check - but native json doesn't do it?
               $val .= $c;
            }
         } elseif ($waitfor && (strpos($waitfor, $c) !== false)) {
            return array($val, $n);  // return current value and state
         } elseif ($state===']') {
            list($v, $n) = json_decode($json, $assoc, $limit, $n, 0, ",]");
            $val[] = $v;
            if ($json[$n] == "]") { return array($val, $n); }
         } elseif ($state==='}') {
            list($i, $n) = json_decode($json, $assoc, $limit, $n, 0, ":");   // this allowed non-string indicies
            list($v, $n) = json_decode($json, $assoc, $limit, $n+1, 0, ",}");
            $val[$i] = $v;
            if ($json[$n] == "}") { return array($val, $n); }
         } else {
         
            #-> whitespace
            if (preg_match("/\s/", $c)) {
               // skip
            } elseif ($c == '"') {
               $state = '"';
            } elseif ($c == "{") {
               list($val, $n) = json_decode($json, $assoc, $limit-1, $n+1, '}', "}");
               
               if ($val && $n) {
                  $val = $assoc ? (array)$val : (object)$val;
               }
            } elseif ($c == "[") {
               list($val, $n) = json_decode($json, $assoc, $limit-1, $n+1, ']', "]");
            } elseif (($c == "/") && ($json[$n+1]=="*")) {
               // just find end, skip over
               ($n = strpos($json, "*/", $n+1)) or ($n = strlen($json));
            } elseif (preg_match("#^(-?\d+(?:\.\d+)?)(?:[eE]([-+]?\d+))?#", substr($json, $n), $uu)) {
               $val = $uu[1];
               $n += strlen($uu[0]) - 1;
               if (strpos($val, ".")) {  // float
                  $val = (float)$val;
               } elseif ($val[0] == "0") {  // oct
                  $val = octdec($val);
               } else {
                  $val = (int)$val;
               }
               // exponent?
               if (isset($uu[2])) {
                  $val *= pow(10, (int)$uu[2]);
               }
            } elseif (preg_match("#^(true|false|null)\b#", substr($json, $n), $uu)) {
               $val = $lang_eq[$uu[1]];
               $n += strlen($uu[1]) - 1;
            } else {
               // PHPs native json_decode() breaks here usually and QUIETLY
              trigger_error("json_decode: error parsing '$c' at position $n", E_USER_WARNING);
               return $waitfor ? array(NULL, 1<<30) : NULL;
            }
         }//state
         
         #-- next char
         if ($n === NULL) { return NULL; }
         $n++;
      }//for
      #-- final result
      return ($val);
   }
}

//+++++++++++++++++++++++++++++
// curl_setopt_array
//+++++++++++++++++++++++++++++
if (!function_exists('curl_setopt_array')) {
   function curl_setopt_array(&$ch, $curl_options){
       foreach ($curl_options as $option => $value) {
           if (!curl_setopt($ch, $option, $value)) {
               return false;
           } 
       }
       return true;
   }
}
//+++++++++++++++++++++++++++++
// hash_hmac
//+++++++++++++++++++++++++++++
if (!function_exists('hash_hmac')) {
    require dirname(__FILE__) .'/hash/hash_hmac.php';
}
?>