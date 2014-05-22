<?php
//============================================
// class_template.php
//  11/10/28 : Smartyを参考に大幅修正
//============================================

//+++++++++++++++++++++++++++++
// テンプレートクラス
//+++++++++++++++++++++++++++++
class class_template{
        var $Template;
        var $Include;
        var $Vars;
        var $TemplateList;
        // デリミタ
        var $left_delimiter = "<?";
        var $right_delimiter = "?>";
        var $html_Encoding = "UTF-8";
        var $system_Encoding = "UTF-8";
        //-----------------------------
        // コンストラクタ
        //-----------------------------
        function class_template(){
                $this->Template = "";
                $this->TemplateList = array();
                $this->Include = array();
                $this->clear_all_assign();
                $this->html_Encoding = mb_internal_encoding();
                $this->system_Encoding = mb_internal_encoding();
        }
        // 文字コード設定
        function sethtmlEncoding($encode){
                $this->html_Encoding = $encode;
        }
        function setSystemEncoding($encode){
                $this->system_Encoding = $encode;
        }
        //-----------------------------
        // 取得
        //-----------------------------
        function get_template_var($name=null){
                if(isset($name)){
                        return $this->Vars[$name];
                }
                return $this->Vars;
        }
        //-----------------------------
        // 設定
        //-----------------------------
        // ファイル読み込み
        function load($filename){
                $this->clear_all_assign();
                $this->Template = "";
                if( !($fp = @fopen($filename,'rb')) ){
                        return false;
                }
                $length = max(1000,filesize($filename));
                $this->Template = fread($fp,$length);
                fclose($fp);
                return true;
        }
        // 出力
        function get_display_template($set=true){
                $template = $this->Template;
                if($set){
                        // インクルード設定
                        $template = $this->setInclude($template);
                        
                        // 変数設定
                        $template = $this->setTemplatesVars($template);
                }
                return $template;
        }
        function display($set=true){
                print $this->get_display_template($set);
        }
        // テンプレート読み込みの設定
        function setIncludeFile($id,$filename){
                if( !($fp = @fopen($filename,'rb')) ){
                        return false;
                }
                $length = max(1000,filesize($filename));
                $template= fread($fp,$length);
                fclose($fp);
                
                $this->Include[$id] = $template;
                return true;
        }
        // データ設定
        function assign($id,$val){
                $this->Vars[$id] = $val;
        }
        function assign_vars($value){
                foreach($value as $key => $val){
                        $this->assign($key,$val);
                }
        }
        // 全て初期化
        function clear_all_assign(){
                $this->Vars = array();
        }
        //-----------------------------
        // 内部関数
        //-----------------------------
        // 属性値を取得
        function getAttr(&$attr,$str){
                $attr = array();
                // key=valueで分割
                $preg_str = "/(\S+)=(\S+)/";
                if(preg_match_all($preg_str ,$str ,$matchs)){
                        $cnt = count($matchs[0]);
                        for($key=0;$key<$cnt;$key++){
                                $name = strtoupper($matchs[1][$key]);
                                $val = $matchs[2][$key];
                                $val = $this->convertString($val);
                                $attr[$name] = $val;
                        }
                }
                return $attr;
        }
        // 文字列を評価
        function evaString($str){
                $str = trim($str);
                // 条件式を取得
                $preg_str = "/^\s*?(\S+?)\s*?([\!\<\>\+\-\*\/%=]+)\s*?(\S+)\s*?$/";
                if(preg_match($preg_str , $str, $tp)){
                        $i1 = $this->convertString($tp[1]);
                        $is = $tp[2];
                        $i2 = $this->convertString($tp[3]);
                        //if(!is_null($i1) && !is_null($i2)){
                                switch((string)$is){
                                case '===':     $item = ($i1 === $i2);      break;
                                case '==':      $item = ($i1 == $i2);       break;
                                case '<=':      $item = ($i1 <= $i2);       break;
                                case '>=':      $item = ($i1 >= $i2);       break;
                                case '<':       $item = ($i1 < $i2);        break;
                                case '>':       $item = ($i1 > $i2);        break;
                                case '!=':      $item = ($i1 != $i2);       break;
                                case '+':       $item = ($i1 + $i2);        break;
                                case '-':       $item = ($i1 - $i2);        break;
                                case '*':       $item = ($i1 * $i2);        break;
                                case '/':       $item = ($i1 / $i2);        break;
                                case '%':       $item = ($i1 % $i2);        break;
                                }
                        //}
                }else{
                        $item = $this->convertString($str);
                }
                return $item;
        }
        function convertString($str,$encode = true){
                // 関数
                $check = true;
                if(preg_match("/^([a-zA-Z][a-zA-Z0-9_]+)\(([\s\S]*)\)$/" ,$str ,$matchs)){
                        $check = false;
                        $val = $this->convertString($matchs[2],false);
                        switch($matchs[1]){
                        case 'is_array':        $str = is_array($val);          break;
                        case 'is_numeric':      $str = is_numeric($val);        break;
                        case 'is_string':       $str = is_string($val);         break;
                        // escape
                        case 'escape':          $str = htmlspecialchars($val,ENT_QUOTES,$this->system_Encoding);       break;
                        case 'htmlentities':    $str = htmlentities($val,ENT_COMPAT,$this->system_Encoding);           break;
                        case 'htmlspecialchars':    $str = htmlspecialchars($val,ENT_COMPAT,$this->system_Encoding);           break;
                        case 'escape_br':       $str = htmlentities($val,ENT_COMPAT,$this->system_Encoding); $str = nl2br($str);  break;
                        case 'quotes':          $str = preg_replace("/\"/","\\\"",$val);        break;
                        case 'urlencode':       $str = urlencode($val);                         break;
                        // format
                        case 'number_format':   $str = number_format($val);     break;
                        case 'count':           $str = count($val);             break;
                        default:                $check = true;                  break;
                        }
                }
                $result = $str;
                if($check){
                        // 文字列
                        if(preg_match("/^\"([\s\S]*)\"$/" ,$str ,$matchs)){
                                $result = (string)$matchs[1];
                        }elseif(preg_match("/^'([\s\S]*)'$/" ,$str ,$matchs)){
                                $result = (string)$matchs[1];
                        // 配列
                        }elseif(preg_match("/^\\\$([\[\]_a-zA-Z0-9\.\\\$]+)$/" ,$str ,$matchs)){
                                // ,で分割
                                $vlist = explode(".",$matchs[1]);
                                foreach($vlist as $v){
                                        // 内部
                                        if(preg_match("/^([_a-zA-Z0-9]+)(\[([\\\$_a-zA-Z0-9]+)\])?$/" ,$v ,$m)){
                                                $key = $m[1];
                                                if(isset($value)){
                                                        if(!isset($value[$key])){
                                                                if(!array_key_exists($key,$value)){
                                                                        trigger_error("template : not found [".$matchs[1]."] value;<br />",E_USER_WARNING);
                                                                }
                                                                $value = NULL;
                                                        }else{
                                                                $value = $value[$key];
                                                        }
                                                }else{
                                                        $value = $this->Vars[$key];
                                                }
                                                if(isset($m[3])){
                                                        $key = $this->convertString($m[3]);
                                                        $value = $value[$key];
                                                }
                                        }
                                }
                                $result = $value;
                        }elseif(is_numeric($str)){
                                $result = intval($str);
                        }elseif(strtoupper($str) == "TRUE"){
                                $result = true;
                        }elseif(strtoupper($str) == "FALSE"){
                                $result = false;
                        }elseif(strtoupper($str) == "NULL"){
                                $result = NULL;
                        }
                }
                if(is_string($result) && $encode && ($this->html_Encoding != $this->system_Encoding)){
                        $result = mb_convert_encoding($result,$this->html_Encoding,$this->system_Encoding);
                }
                return $result;
                //return $this->left_delimiter.$str.$this->right_delimiter;
        }
        // 読み込みテンプレートの設定
        function setInclude($template){
                //$preg_str = "/([\s\S]*?)".preg_quote($this->left_delimiter)."INCLUDE\s+([\s\S]+?)".preg_quote($this->right_delimiter)."([\s\S]*?)/i";
                $preg_str = "/".preg_quote($this->left_delimiter,"/")."INCLUDE\s+([\s\S]+?)".preg_quote($this->right_delimiter,"/")."/i";
                $template = preg_replace_callback( $preg_str , array($this, '_setIncludeCallback') , $template);
                return $template;
        }
        function _setIncludeCallback($args){
                //$tp_header = $args[1];
                //$var = $args[2];
                //$tp_footer = $args[3];
                $var = $args[1];
                // 属性値を取得
                $attr = $this->getAttr($attr,$var);
                // 属性から値設定
                $tmp = "";
                foreach($attr as $name => $val){
                        switch($name){
                        case "FILE":
                                $tmp = $this->Include[$val];
                                break;
                        }
                }
                //$template = $tp_header.$tmp.$tp_footer;
                //return $template;
                return $tmp;
        }
        // テンプレートの設定
        function setTemplatesVars($template){
                $preg_str = "/".preg_quote($this->left_delimiter,"/")."([\s\S]+?)".preg_quote($this->right_delimiter,"/")."/";
                // 文字列の分割
                $matchs = preg_split($preg_str ,$template ,0,PREG_SPLIT_DELIM_CAPTURE);
                $this->TemplateList = $matchs;
                $cnt = count($matchs);
                // テンプレートを評価
                $tmp = $matchs[0];
                $level= 0;
                for($key=1;$key<$cnt;$key+=2){
                        $this->_setTemplateTags($tmp,$key,$matchs,$level);
                }
                return $tmp;
        }
        // タグの実装
        function _setTemplateTags(&$tmp,&$key,&$list,&$level,$check=false,$skip=false){
                // 式を取得
                $ptn = $list[$key];
                // foreach
                if(preg_match("/^(\/?)FOREACH\s*([\s\S]+?)$/i" ,$ptn ,$m)){
                        // 終了タグ
                        if($m[1] == "/"){
                                $level --;
                        // 開始タグ
                        }else{
                                $level ++;
                                if($skip == false){
                                        // ループ処理
                                        $this->_setForeachLoop($m[2],$tmp,$key,$list,$level);
                                }else{
                                        // タグスキップ
                                        $this->_skipIfTags($tmp,$key,$list,$level);
                                }
                        }
                // for文
                }else if(preg_match("/^(\/?)FOR\s*([\s\S]+?)$/i" ,$ptn ,$m)){
                        // 終了タグ
                        if($m[1] == "/"){
                                $level --;
                        // 開始タグ
                        }else{
                                $level ++;
                                if($skip == false){
                                        // ループ処理
                                        $this->_setForLoop($m[2],$tmp,$key,$list,$level);
                                }else{
                                        // タグスキップ
                                        $this->_skipIfTags($tmp,$key,$list,$level);
                                }
                        }
                // if文
                }else if(preg_match("/^IF\s*\(\s*([\s\S]+?)\)$/i" ,$ptn ,$m)){
                        $level ++;
                        if($skip == false){
                                // IF処理
                                $this->_setIf($m[1],$tmp,$key,$list,$level);
                        }else{
                                // タグスキップ
                                $this->_skipIfTags($tmp,$key,$list,$level);
                        }
                // if文終了
                }else if(preg_match("/^\/IF$/i" ,$ptn ,$m)){
                        $level --;
                // 無効な値は無視
                }else{
                        if($skip == false){
                                $var = $this->evaString($ptn);
                                $tmp .= $var;
                                $tmp .= $list[$key+1];
                        }
                }
                return $check;
        }
        // タグをスキップ
        function _skipIfTags(&$tmp,&$key,&$list,&$level){
                $cnt = count($list);
                $start_level = $level - 1;
                for($key+=2;$key<$cnt;$key+=2){
                        // タグの実装
                        $this->_setTemplateTags($tmp,$key,$list,$level,true,true);
                        // レベルチェック
                        if($level <= $start_level){
                                //echo str_repeat("　",$level).htmlspecialchars($list[$key])."A<br/>";
                                break;
                        }
                }
        }
        // forループを実装
        function _setForLoop($str,&$tmp,&$key,&$list,&$level){
                // 要素を抽出
                preg_match("/^\s*\\\$([\S]+)=([\S]+)\s+TO\s+(\S+)\s*((\S+)\s*)?$/i" ,$str ,$m);
                $name = $m[1];
                $start = $this->convertString($m[2]);
                $loop = $this->convertString($m[3]);
                $step = 1;
                if(isset($m[5])){
                        $at = $m[5];
                        // 属性値を取得
                        $attr = $this->getAttr($attr,$at);
                        if(isset($attr["STEP"])){
                                $step = intval($attr["STEP"]);
                        }
                }
                $start_key = $key;
                $start_level = $level - 1;
                $cnt = count($list);
                if($start < $loop){
                        for($i=$start;$i<=$loop;$i+=$step){
                                $this->assign($name,$i);
                                $tmp .= $list[$key + 1];
                                $key += 2;
                                while($key < $cnt){
                                        // タグの実装
                                        $this->_setTemplateTags($tmp,$key,$list,$level);
                                        // レベルチェック
                                        if($level <= $start_level){
                                                if(($i + $step) <= $loop){
                                                        // キー値を戻す
                                                        $key = $start_key;
                                                        $level ++;
                                                }
                                                break;
                                        }
                                        $key += 2;
                                };
                        }
                        $tmp .= $list[$key + 1];
                }else{
                        // タグスキップ
                        $this->_skipIfTags($tmp,$key,$list,$level);
                        $tmp .= $list[$key + 1];
                }
        }
        
        // foreachループを実装
        function _setForeachLoop($str,&$tmp,&$key,&$list,&$level){
                // 要素を抽出
                preg_match("/^\s*([\S]+)\s+AS\s+([\S\s]+?)\s*$/i" ,$str ,$m);
                $item_list = $this->convertString($m[1]);
                $loop_key = "";
                $name = "";
                $s = $m[2];
                if(preg_match("/^\\\$([\S]+)\s*=>\s*\\\$([\S]+)$/" ,$s ,$m)){
                        $loop_key = $m[1];
                        $name = $m[2];
                }elseif(preg_match("/^\\\$([\S]+)$/" ,$s ,$m)){
                        $name = $m[1];
                }
                // セクションの設定
                $start_key = $key;
                $start_level = $level - 1;
                $cnt = count($list);
                $item_cnt = count($item_list);
                if(0 < $item_cnt){
                        $i = 0;
                        foreach($item_list as $item_key => $item){
                                if($loop_key != ""){
                                        $this->assign($loop_key,$item_key);
                                }
                                $this->assign($name,$item);
                                
                                $tmp .= $list[$key + 1];
                                $key += 2;
                                while($key < $cnt){
                                        // タグの実装
                                        $this->_setTemplateTags($tmp,$key,$list,$level);
                                        // レベルチェック
                                        if($level <= $start_level){
                                                if(($i + 1) < $item_cnt){
                                                        // キー値を戻す
                                                        $key = $start_key;
                                                        $level ++;
                                                }
                                                break;
                                        }
                                        $key += 2;
                                };
                                $i ++;
                        }
                        $tmp .= $list[$key + 1];
                }else{
                        // タグスキップ
                        $this->_skipIfTags($tmp,$key,$list,$level);
                        $tmp .= $list[$key + 1];
                }
        }
        /*
        // セクションのループを実装
        function _setSectionLoop($at,&$tmp,&$key,&$list,&$level){
                // 属性値を取得
                $attr = $this->getAttr($attr,$at);
                // 属性値を評価
                $name = $attr["NAME"];
                $start = 0;
                $step = 1;
                $loop = 0;
                $keys = array();
                $set = "";
                if(isset($attr["START"])){
                        $start = intval($attr["START"]);
                }
                if(isset($attr["STEP"])){
                        $step = intval($attr["STEP"]);
                }
                if(is_array($attr["LOOP"])){
                        $loop = count($attr["LOOP"]);
                        $keys = array_keys($attr["LOOP"]);
                }else{
                        $loop = intval($attr["LOOP"]) + $start;
                        for($i=0;$i<$loop;$i++){
                                $keys[] = $i;
                        }
                }
                if(isset($attr["SET"])){
                        $set = $attr["SET"];
                }
                // セクションの設定
                $this->Vars[$name] = $start;
                $start_key = $key;
                $start_level = $level - 1;
                $cnt = count($list);
                $kcnt = count($keys);
                if($start < $kcnt){
                        //for($this->Vars[$name]=$start;$this->Vars[$name]<$loop,$this->Vars[$name]<$kcnt;$this->Vars[$name]+=$step){
                        for($i=$start;$i<$kcnt;$i+=$step){
                                $this->Vars[$name] = $keys[$i];
                                // 変数セット
                                if($set != ""){
                                        $k = $this->Vars[$name];
                                        if(is_array($attr["LOOP"])){
                                                $this->assign($set,$attr["LOOP"][$k]);
                                        }else{
                                                $this->assign($set,$k);
                                        }
                                }
                                $tmp .= $list[$key + 1];
                                $key += 2;
                                while($key < $cnt){
                                        // タグの実装
                                        $this->_setTemplateTags($tmp,$key,$list,$level);
                                        // レベルチェック
                                        if($level <= $start_level){
                                                if(($i + $step) < $kcnt){
                                                        // キー値を戻す
                                                        $key = $start_key;
                                                        $level ++;
                                                }
                                                break;
                                        }
                                        $key += 2;
                                };
                        }
                        $tmp .= $list[$key + 1];
                }else{
                        // タグスキップ
                        $this->_skipIfTags($tmp,$key,$list,$level);
                        $tmp .= $list[$key + 1];
                }
        }*/
        // if文を実装
        function _setIf($str,&$tmp,&$key,&$list,&$level){
                // if処理
                $check = $this->_setIfExecute($str,$tmp,$key,$list,$level);
                
                $start_level = $level - 1;
                $cnt = count($list);
                $loop = 0;
                for(;$key<$cnt;$key+=2){
                        //echo str_repeat("　",$level).htmlspecialchars($list[$key])."A<br/>";
                        // 式を取得
                        $ptn = $list[$key];
                        // elseif文
                        if(preg_match("/^(\/?)ELSE\s*IF\s*\(\s*([\s\S]+?)\)$/i" ,$ptn ,$m)){
                                       //echo "skip";
                                if($check == false){
                                        // if文法処理
                                        if($check = $this->_setIfExecute($m[2],$tmp,$key,$list,$level,$check)){
                                        }
                                        $key -= 2;
                                        continue;
                                }else{
                                        // タグスキップ
                                        $this->_skipIfTags($tmp,$key,$list,$level);
                                }
                        // else文
                        }else if(preg_match("/^(\/?)ELSE\s*$/i" ,$ptn ,$m)){
                                if($check == false){
                                        // if文法処理
                                        if($check = $this->_setIfExecute(NULL,$tmp,$key,$list,$level,$check)){
                                        }
                                        $key -= 2;
                                        continue;
                                }else{
                                        // タグスキップ
                                        $this->_skipIfTags($tmp,$key,$list,$level);
                                }
                        }else{
                                $this->_setTemplateTags($tmp,$key,$list,$level,$check,($check == false));
                        }
                        // レベルチェック
                        if($level <= $start_level){
                                $tmp .= $list[$key + 1];
                                break;
                        }
                        $loop ++;
                }
        }
        // if・elseif・else文を処理
        function _setIfExecute($str,&$tmp,&$key,&$list,&$level,$checked=false){
                // チェック
                $check = true;
                if($str != NULL){
                        $check = $this->_setIfCheck($str);
                }
                if($check && $checked == false){
                        $tmp .= $list[$key + 1];
                }
                $key += 2;
                return ($check | $checked);
        }
        function _setIfCheck($str){
                $check = false;
                if($this->evaString($str)){
                        $check = true;
                }
                return $check;
        }
/*
      var $var_cb = '';
      var $tempValue = NULL;
//-----------------------------
// 取得関数
//-----------------------------
      // テンプレートの読み込み
      function loadTemplate($filename){
            if( !($fp = @fopen($filename,'rb')) ){
                  return NULL;
            }
            $load = fread($fp,filesize($filename));
            fclose($fp);
            return $load;
      }
      // テンプレートエリアを置換
      function replaceArea($template_start,$template_end,$template,$tmp){
           // テンプレートエリアの取得
           list($tmp_header,$tmp_body,$tmp_footer) = $this->getArea($template_start,$template_end,$template);
           $template = $tmp_header.$tmp.$tmp_footer;
           return $template;
      }
      // テンプレートエリアタグを削除
      function removeAreaTag($template_start,$template_end,$template,$tmp){
           // テンプレートエリアの取得
           list($tmp_header,$tmp_body,$tmp_footer) = $this->getArea($template_start,$template_end,$template);
           $template = $tmp_header.$tmp_body.$tmp_footer;
           return $template;
      }
      // テンプレートエリアの取得
      function getArea($template_start,$template_end,$template){
            $preg_str = "/^([\s\S]*)".preg_quote($template_start,'/').'([\s\S]+?)'.preg_quote($template_end,'/')."([\s\S]*)\$/";
            if( preg_match( $preg_str , $template , $tp , PREG_OFFSET_CAPTURE ) ){
                  return array($tp[1][0],$tp[2][0],$tp[3][0]);
            }
            return NULL;
      }
      // テンプレートを読み込み設定
      function setLoadTemplateParts($template,$var_name,$filename){
            // テンプレートの読み込み
            $tpl = $this->loadTemplate($filename);
            // テンプレートに設定
            return $this->setTemp($var_name,$tpl,$template);
      }
//-----------------------------
// 設定関数
//-----------------------------
      // テンプレートに変数を当てはめる(簡易型)
      function setTemp($v,$temp,$template){
            $preg_str = "/<\?\s*".preg_quote($v,"/")."\s*\?>/";
            $template = preg_replace($preg_str,$temp,$template);
            return $template;
      }
      // テンプレートに変数を当てはめる
      function setValue($valuelist,$template){
            if(is_array($valuelist)){
                  foreach($valuelist as $key => $val){
                        $template = $this->setTemp($key,$val,$template);
                  }
            }
            return $template;
      }
      // テンプレートに条件式を当てはめる(簡易型)
      function _setIfTemp($template){
            $preg_str = "/<\!--[\s]*if[\s]*\([\s]*([\s\S]*?)[\s]*\)[\s]*-->([\s\S]*?)<\!--[\s]*end[\s]*if[\s]*-->/";
            $template = preg_replace_callback( $preg_str , array($this, '_temp_cb_if_func') , $template );
            return $template;
      }
      function setIfTemp($v,$temp,$template){
            $this->tempValue = array($v => $temp);
            return $this->_setIfTemp($template);
      }
      // テンプレートに条件式を当てはめる
      function setIf($valuelist,$template){
            if(is_array($valuelist)){
                  $this->tempValue = $valuelist;
                  foreach($valuelist as $key => $val){
                        $template = $this->_setIfTemp($template );
                  }
            }
            return $template;
      }
      // テンプレートに多重判定式を当てはめる
      function _setSwitchTemp($template){
            $preg_str = "/<\!--[\s]*switch[\s]*\([\s]*([\s\S]*?)[\s]*\)[\s]*-->([\s\S]*?)<\!--[\s]*end[\s]*switch[\s]*-->/";
            $template = preg_replace_callback( $preg_str , array($this, '_temp_cb_switch_func') , $template );
            return $template;
      }
      function setSwitchTemp($v,$temp,$template){
            $this->tempValue = array($v => $temp);
            return $this->_setSwitchTemp($template);
      }
      function setSwitch($valuelist,$template){
            if(is_array($valuelist)){
                  $this->tempValue = $valuelist;
                  foreach($valuelist as $key => $val){
                        $template = $this->_setSwitchTemp($template );
                  }
            }
            return $template;
      }
      // テンプレートにテンプレートプログラムを設定
      function templateExec($valuelist,$template){
            $template = $this->setSwitch($valuelist,$template);
            $template = $this->setIf($valuelist,$template);
            $template = $this->setValue($valuelist,$template);
            return $template;
      }
      // テンプレートにテンプレートプログラムを設定(配列)
      function templateExecArray($template_start,$template_end,$valuelist,$template){
            while(list($tp_header,$tp_body,$tp_footer) = $this->getArea($template_start,$template_end,$template)){
                  $tmp_body = '';
                  foreach($valuelist as $list){
                        // テンプレートに設定
                        $tmp = $this->templateExec($list,$tp_body);
                        $tmp_body .= $tmp;
                  }
                  $template = $tp_header.$tmp_body.$tp_footer;
            }
            return $template;
      }
//-----------------------------
// 内部関数
//-----------------------------
      // 格納された変数を取得
      function getTempValue($str){
            if( preg_match( "/^\\$([0-9a-zA-Z_]+)$/" , $str , $tp , PREG_OFFSET_CAPTURE ) ){
                  if(isset($this->tempValue[$tp[1][0]])){
                        return $this->tempValue[$tp[1][0]];
                  }else{
                        return NULL;
                  }
            }
            return $str;
      }
      function _getTempValueStr($args){
            if( preg_match( "/^\\$([0-9a-zA-Z_]+)\$/" , $args , $tp , PREG_OFFSET_CAPTURE ) ){
                  if(isset($this->tempValue[$tp[1][0]]) && is_null($this->tempValue[$tp[1][0]]) != true){
                        return $this->tempValue[$tp[1][0]];
                  }else{
                        return NULL;
                  }
            }
            return $args;
      }
      // if文コールバック関数
      function _temp_cb_if_func($args){
            $body = $args[2];
            $else_body = "";
            // if条件式と内容を分割
            $preg_str = "/<\!--[\s]*else[\s]*if\([\s]*([\s\S]*?)[\s]*\)[\s]*-->/";
            $result = preg_split($preg_str,$body,-1,PREG_SPLIT_DELIM_CAPTURE);
            
            $if_list = array();
            $if_list[] = $args[1];
            $last_body = array_pop($result);
            foreach($result as $val){
                $if_list[] = $val;
            }
            // else文検出
            $preg_str = "/^([\s\S]*?)<\!--[\s]*else[\s]*-->([\s\S]*)/";
            if(preg_match($preg_str,$last_body,$result)){
                $if_list[] = $result[1];
                $else_body = $result[2];
            }else{
                $if_list[] = $last_body;
            }
            // 条件式を取得
            $cnt = count($if_list);
            for($i=0;$i<$cnt;$i+=2){
                $success = false;
                // 条件式を取得
                $preg_str = "/(\S+)\s*([\!\<\>=]+)\s*(\S+)/";
                if( preg_match( $preg_str , $if_list[$i] , $tp , PREG_OFFSET_CAPTURE )){
                        $i1 = $this->_getTempValueStr($tp[1][0]);
                        $is = $tp[2][0];
                        $i2 = $this->_getTempValueStr($tp[3][0]);
                        if(!is_null($i1) && !is_null($i2)){
                                switch((string)$is){
                                case '==':      if($i1 === $i2) $success = true;       break;
                                case '<=':      if($i1 <= $i2)  $success = true;       break;
                                case '>=':      if($i1 >= $i2)  $success = true;       break;
                                case '<':       if($i1 < $i2)   $success = true;       break;
                                case '>':       if($i1 > $i2)   $success = true;       break;
                                case '!=':      if($i1 != $i2)  $success = true;       break;
                                }
                        }
                }else{
                        $item = $this->_getTempValueStr($if_str);
                        if($item != NULL){
                                if(is_numeric($item)){
                                        $item = intval($item);
                                }
                                if($item){
                                        $success = true;
                                }
                        }
                }
                if($success){
                        break;
                }
            }
            // else文を取得
            $template = "";
            if($i < $cnt){
                $template = $if_list[$i + 1];
            }else{
                $template = $else_body;
            }
            return $template;
      }
      // switch文コールバック関数
      function _temp_cb_switch_func($args){
            $preg_str = "/<\!--[\s]*case[\s]*([\$a-zA-Z0-9]+)[\s]*-->/";
            $result = preg_split($preg_str,$args[2],-1,PREG_SPLIT_DELIM_CAPTURE);
            if(($value = $this->getTempValue($args[1])) !== NULL){
                    $cnt = count($result);
                    $template = "";
                    for($i=1;$i<$cnt;$i+=2){
                        if($result[$i] == $value){
                                $template = $result[$i+1];
                                break;
                        }
                    }
                return $template;
            }
            return $args[0];
      }*/
}

?>