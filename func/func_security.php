<?php
//============================================
// func_security.php
//============================================

//+++++++++++++++++++++++++++++
// 変数汚染対策
//+++++++++++++++++++++++++++++
function protector_sanitize( $arr ){
      if(is_array($arr)){
            // 変数汚染攻撃対策
            if(!empty($arr['_SESSION']) || !empty($arr['_COOKIE']) || !empty($arr['_SERVER']) || !empty($arr['_ENV']) || !empty($arr['_FILES']) || !empty($arr['GLOBALS'])){
            	exit;
            }
            return array_map('protector_sanitize',$arr);
      }
      // ヌルバイト攻撃対策
      return str_replace("\0","",$arr);
}
// グローバル変数攻撃対策
$_GET = protector_sanitize($_GET);
$_POST = protector_sanitize($_POST);
$_COOKIE = protector_sanitize($_COOKIE);

// セッション鍵を利用したXSS、HTTPレスポンス分割対策
// POSTやURLに埋め込まれたセッション鍵はあえて無視する
function use_only_cookies(){
        // セッション鍵を利用したXSS、HTTPレスポンス分割対策
        // POSTやURLに埋め込まれたセッション鍵はあえて無視する
        ini_set('session.use_only_cookies',1);
}
// セッション固定攻撃を避けるためにはココより下の2行をコメントアウトする
//if(!empty($_GET[ session_name() ]) || !empty($_COOKIE[ session_name() ]) || !preg_match('/[^0-9A-Za-z,-]/',$_GET[ session_name() ])){
// $_COOKIE[ session_name() ] = $_GET[ session_name() ];
//}

// PHP_SELFを利用したXSSおよびHTTPレスポンス攻撃への対策
$_SERVER['PHP_SELF'] = strtr(@$_SERVER['PHP_SELF'],array('<' => '%3C','>' => '%3E',"'" => '%27','"' => '%22',"\r" => '',"\n" => ''));
$_SERVER['PHP_INFO'] = strtr(@$_SERVER['PHP_INFO'],array('<' => '%3C','>' => '%3E',"'" => '%27','"' => '%22',"\r" => '',"\n" => ''));
?>