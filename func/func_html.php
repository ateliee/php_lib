<?php
//============================================
// func_html.php
//============================================
//--------------------------------------------
// パラメーター作成
//--------------------------------------------
function makeUrlParameter($value,$null_key=null){
      $param = "";
      foreach($value as $key => $val){
            if(isset($null_key) && ($null_key == $val)){
                  continue;
            }
            if($param != ""){
                  $param .= "&";
            }
            $param .= $key."=".$val;
      }
      return $param;
}
//--------------------------------------------
// チェックボックス補助
//--------------------------------------------
function htmlChecked($flag){
      if($flag == true || $flag > 0){
            return 'checked="checked"';
      }
      return '';
}

//--------------------------------------------
// 数値のリストを取得
//--------------------------------------------
function makeNumsOpts($code,$max,$default="",$default_value="0"){
      return makeNumsOptsExp($code,1,$max,1,$default,$default_value);
}

//--------------------------------------------
// 数値のリストを取得(拡張版)
//--------------------------------------------
function makeNumsOptsExp($code,$start,$max,$skip=1,$default="",$default_value="0"){
      // 一覧に設定
      $list = "";
      if($default != ""){
            $list .= '<option value="'.$default_value.'">'.htmlentities($default,ENT_QUOTES, mb_internal_encoding()).'</option>'."\n";
      }
      for($i=$start;$i<=$max;$i+=$skip){
            $key = $i;
            $num = $i;
            if($code == $i){
                  $tmp = '<option value="'.$key.'" selected="selected">'.$num.'</option>'."\n";
            }else{
                  $tmp = '<option value="'.$key.'">'.$num.'</option>'."\n";
            }
            $list .= $tmp;
      }
      return $list;
}

//--------------------------------------------
// 連想配列のリストを取得
//--------------------------------------------
function makeValueOpts($code,$value,$default="",$default_value="0"){
      // 一覧に設定
      $list = "";
      if($default != ""){
            $list .= '<option value="'.$default_value.'">'.htmlentities($default,ENT_QUOTES, mb_internal_encoding()).'</option>'."\n";
      }
      foreach($value as $key => $val){
            if($code == $key){
                  $tmp = '<option value="'.$key.'" selected="selected">'.$val.'</option>'."\n";
            }else{
                  $tmp = '<option value="'.$key.'">'.$val.'</option>'."\n";
            }
            $list .= $tmp;
      }
      return $list;
}

//--------------------------------------------
// 連想配列のリストを取得(グループ)
//--------------------------------------------
function makeValueOptGroup($code,$valuelist,$default="",$default_value="0"){
      // 一覧に設定
      $list = "";
      if($default != ""){
            $list .= '<option value="'.$default_value.'">'.htmlentities($default,ENT_QUOTES, mb_internal_encoding()).'</option>'."\n";
      }
      foreach($valuelist as $list_key => $value){
              $list .= '<optgroup label="'.$list_key.'">';
              foreach($value as $key => $val){
                    if($code == $key){
                          $tmp = '<option value="'.$key.'" selected="selected">'.$val.'</option>'."\n";
                    }else{
                          $tmp = '<option value="'.$key.'">'.$val.'</option>'."\n";
                    }
                    $list .= $tmp;
              }
              $list .= '</optgroup>';
      }
      return $list;
}

//--------------------------------------------
// 多次元連想配列のリストを取得
//--------------------------------------------
function makeValuelistOpts($code,$value,$value_key,$default="",$default_value="0"){
      // 一覧に設定
      $list = "";
      if($default != ""){
            $list .= '<option value="'.$default_value.'">'.htmlentities($default,ENT_QUOTES, mb_internal_encoding()).'</option>'."\n";
      }
      foreach($value as $key => $val){
            if($code == $key){
                  $tmp = '<option value="'.$key.'" selected="selected">'.$val[$value_key].'</option>'."\n";
            }else{
                  $tmp = '<option value="'.$key.'">'.$val[$value_key].'</option>'."\n";
            }
            $list .= $tmp;
      }
      return $list;
}

//--------------------------------------------
// パンくずリストを生成
//--------------------------------------------
function makePnkz($name,$href=""){
      return array("href"=>$href,"value"=>$name);
}
function getPnkz($pnkz,$options=array()){
      $count = count($pnkz);
      
      $start = "";
      if(isset($options["start"])){
            $start = $options["start"];
      }
      $end = "";
      if(isset($options["end"])){
            $end = $options["end"];
      }
      $mode = "";
      if(isset($options["mode"])){
            $mode = $options["mode"];
      }
      // 一覧に設定
      $list = "";
      $i = 0;
      foreach($pnkz as $key => $val){
            $list .= $start;
            if($mode == "list"){
                $list .= '<li class="li'.$i.'">';
            }
            if((($i + 1) < $count) && ($val["href"])){
                  $list .= '<a href="'.$val["href"].'">'.$val["value"].'</a>';
            }else{
                  $list .= '<span>'.$val["value"].'</span>';
            }
            if($mode == "list"){
                $list .= '</li>';
            }
            $list .= $end;
            $i ++;
      }
      return $list;
}

//--------------------------------------------
// 自動リンク
//--------------------------------------------
function autoURLLink($str,$attr=""){
        $patterns = "/(https?|ftp)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/i";
        if($attr != ""){
                $attr = " ".$attr;
        }
        $replacements = "<a href=\"\\1\\2\"".$attr.">\\1\\2</a>";
        return preg_replace($patterns, $replacements, $str);
}
function autoTelLink($str,$attr=""){
        $patterns = "/(0\d{9,10})/i";
        if($attr != ""){
                $attr = " ".$attr;
        }
        $replacements = "<a href=\"tel:\\1\"".$attr.">\\1</a>";
        return preg_replace($patterns, $replacements, $str);
}
function autoMailLink($str,$attr=""){
        $patterns = "/([a-zA-Z0-9_\.-]+\@)([a-zA-Z0-9_\.-]+)([a-zA-Z]+)/i";
        if($attr != ""){
                $attr = " ".$attr;
        }
        $replacements = "<a href=\"mailto:\\1\\2\\3\"".$attr.">\\1\\2\\3</a>";
        return preg_replace($patterns, $replacements, $str);
}
// br→改行変換
function br2nl($string){
    // 大文字・小文字を区別しない
    return preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/i', "\n", $string);
}
//--------------------------------------------
// チェックボックスリストを生成
//--------------------------------------------
function makeCheckboxList($value_key,$checklist,$valuelist,$before="",$after=""){
        $list = "";
        if(count($valuelist) > 0){
                foreach($valuelist as $key => $value){
                        $checked = false;
                        if(is_array($checklist) && in_array($key,$checklist)){
                                $checked = true;
                        }
                        $list .= $before.'<input type="checkbox" name="'.$value_key.'[]" id="'.$value_key.'_'.$key.'" value="'.$key.'" '.htmlChecked($checked).'> <label for="'.$value_key.'_'.$key.'">'.$value.'</label>'.$after;
                }
        }
        return $list;
}
?>