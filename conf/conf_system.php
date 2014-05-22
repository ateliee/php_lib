<?php
//============================================
// conf_system.php
//============================================
//+++++++++++++++++++++++++++++
// 定数宣言
//+++++++++++++++++++++++++++++
// システムデータ
if(defined('SYSTEM_REQUEST') == false){
      define('SYSTEM_REQUEST',$_SERVER['REQUEST_URI']);
}
if(defined('SYSTEM_DOMAIN') == false){
      define('SYSTEM_DOMAIN',$_SERVER['SERVER_NAME']);
}
?>