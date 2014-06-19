<?php
//============================================
// class_cookie.php
//============================================
//+++++++++++++++++++++++++++++
// クッキークラス
//+++++++++++++++++++++++++++++
class class_cookie{
      // 保持する名前空間名
      var $NameSpace = "";
      var $TimeOut = 0;
// var $Serialize = false;
      var $Path = "";

      //=====================
      // 関数
      //=====================
      // 名前空間の定義
      function init($namespace="",$timeout=0,$path="/"){
            if($namespace != ""){
                  // クッキーデータの初期化
                  $this->NameSpace = $namespace;
                  $this->TimeOut = $timeout;
//             $this->Serialize = $serialize;
                  $this->Path = $path;
                  $cookie_name = $namespace."_S";
//             if($this->Serialize && !isset($_COOKIE[$cookie_name])){
                  if(!isset($_COOKIE[$cookie_name])){
                        $cookArr = array();
                        foreach($_COOKIE as $name => $val){
                              if(strpos($name,$this->NameSpace) !== false){
                                    $subname = substr($name,strlen($this->NameSpace) + 1);
                                    $cookArr[$subname] = $val;
                                    $this->_destroyCookie($name);
                              }
                        }
                        $this->_setCookie($cookArr); 
                  }
                  /*if(!$this->Serialize && isset($_COOKIE[$cookie_name])){
                        $items = unserialize($_COOKIE[$cookie_name]);
                        $this->_destroyCookie($cookie_name);
                        $this->_setCookie($items);
                  }*/
            }
      }
      // 取得関数
      function get($name){
            //if($this->Serialize){
                  $cookie_name = $this->NameSpace."_S";
                  if(isset($_COOKIE[$cookie_name])){
                        $c = unserialize(stripslashes($_COOKIE[$cookie_name]));
                        if(isset($c[$name])){
                              return $c[$name];
                        }
                  }
            /*}else{
                  $cookie_name = $this->NameSpace."_".$name;
                  if(isset($_COOKIE[$cookie_name])){
                        return $_COOKIE[$cookie_name];
                  }
            }*/
            return NULL;
      }
      // チェック関数
      function is($name){
            //if($this->Serialize){
                  $cookie_name = $this->NameSpace."_S";
                  if(isset($_COOKIE[$cookie_name])){
                        $c = unserialize(stripslashes($_COOKIE[$cookie_name]));
                        if(isset($c[$name])){
                              return true;
                        }
                  }
            /*}else{
                  $cookie_name = $this->NameSpace."_".$name;
                  if(isset($_COOKIE[$cookie_name])){
                        return true;
                  }
            }*/
            return false;
      }
      // 破棄
      function destroyAllCookies(){
            foreach($_COOKIE as $name => $val){
                  if(strpos($name,$this->NameSpace) !== false){
                        $_COOKIE[$name] = NULL;
                        $this->_destroyCookie($name);
                  }
            }
      }
      function _destroyCookie($name){
            $stamp = time() - 432000;
            setcookie($name,"",$stamp,$this->Path);
      }
      // 設定関数
      function set($name,$value){
            //if($this->Serialize){
                  $cookie_name = $this->NameSpace."_S";
                  if(isset($_COOKIE[$cookie_name])){
                        $c = unserialize(stripslashes($_COOKIE[$cookie_name]));
                        $c[$name] = $value;
                        $this->_setCookie($c);
                  }
            /*}else{
                  $stamp = time() + $this->TimeOut;
                  $cookie_name = $this->NameSpace."_".$name;
                  setcookie($cookie_name,$value,$stamp,$this->Path);
            }*/
      }
      function _setCookie($items){
            //if($this->Serialize){
                  $sitems = serialize($items);
                  $name = $this->NameSpace . "_S";
                  $_COOKIE[$name] = $sitems;
                  $stamp = time() + $this->TimeOut;
                  setcookie($name,$sitems,$stamp,$this->Path);
            /*}else{
                  $stamp = time() + $this->TimeOut;
                  foreach($items as $name => $val){
                        $cookie_name = $this->NameSpace."_".$name;
                        $_COOKIE[$cookie_name] = $val;
                        setcookie($cookie_name,$val,$stamp,$this->Path);
                  }
            }*/
      }
}

?>