<?php
//============================================
// func_language.php
//============================================
$L_LANGUAGE_ARRAY = array();
$L_LANGUAGE_DEFAULT_ARRAY = array();

//+++++++++++++++++++++++++++++
// 言語を読み込み
//+++++++++++++++++++++++++++++
function language_init($language,$def_language){
      // カスタム言語ファイルを読み込み
      if($language != $def_language){
            if(defined('SYSTEM_PATH_LANGUAGE')){
                  if(defined(SYSTEM_LIB_LANGUAGE)){
                        $path = SYSTEM_PATH_LANGUAGE.SYSTEM_LIB_LANGUAGE.".txt";
                        if(file_exists($path)){
                              $array = file($path);
                        }
                        $path = SYSTEM_PATH_LANGUAGE.$L_LANGUAGE_DEFAULT.".txt";
                        if(file_exists($path)){
                              $array = file($path);
                        }
                  }
            }
      }
      return true;
}

//+++++++++++++++++++++++++++++
// 言語を設定
//+++++++++++++++++++++++++++++
function _e($value){
      if(count($L_LANGUAGE_ARRAY) > 0 && count($L_LANGUAGE_ARRAY) > 0){
            foreach($L_LANGUAGE_DEFAULT_ARRAY as $key => $val){
                  if($val == $value){
                        return $L_LANGUAGE_ARRAY[$key];
                  }
            }
      }
}

?>