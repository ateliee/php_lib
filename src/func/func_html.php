<?php
/**
 * パラメーター作成
 *
 * @param $value
 * @param null $null_key
 * @return string
 */
function makeUrlParameter($value, $null_key = null)
{
    $param = "";
    foreach ($value as $key => $val) {
        if (isset($null_key) && ($null_key == $val)) {
            continue;
        }
        if ($param != "") {
            $param .= "&";
        }
        $param .= $key . "=" . $val;
    }
    return $param;
}

/**
 * チェックボックス補助
 *
 * @param $flag
 * @return string
 */
function htmlChecked($flag)
{
    if ($flag == true || $flag > 0) {
        return 'checked="checked"';
    }
    return '';
}

/**
 * @param $flag
 * @return string
 */
function htmlDisabled($flag)
{
    if ($flag == true || $flag > 0) {
        return 'disabled="disabled"';
    }
    return '';
}

/**
 * @param $flag
 * @return string
 */
function htmlReadonly($flag)
{
    if ($flag == true || $flag > 0) {
        return 'readonly="readonly"';
    }
    return '';
}

/**
 * 数値のリストを取得
 *
 * @param $code
 * @param $max
 * @param string $default
 * @param string $default_value
 * @return string
 */
function makeNumsOpts($code, $max, $default = "", $default_value = "0")
{
    return makeNumsOptsExp($code, 1, $max, 1, $default, $default_value);
}

/**
 * @param $key
 * @param $val
 * @param bool $selected
 * @return string
 */
function makeOptionTag($key,$val,$selected=false){
    return '<option value="' . $key . '"'.($selected ? ' selected="selected"' : '').'>' . htmlentities($val, ENT_QUOTES, mb_internal_encoding()) . '</option>' . "\n";
}

/**
 * 数値のリストを取得(拡張版)
 *
 * @param $code
 * @param $start
 * @param $max
 * @param int $skip
 * @param string $default
 * @param string $default_value
 * @return string
 */
function makeNumsOptsExp($code, $start, $max, $skip = 1, $default = "", $default_value = "0")
{
    // 一覧に設定
    $list = "";
    if ($default != "") {
        $list .= makeOptionTag($default_value,$default);
    }
    for ($i = $start; $i <= $max; $i += $skip) {
        $tmp = makeOptionTag($i,$i,is_array($code) ? in_array($i,$code) : ($code == $i));
        $list .= $tmp;
    }
    return $list;
}

/**
 * 連想配列のリストを取得
 *
 * @param $code
 * @param $value
 * @param string $default
 * @param string $default_value
 * @return string
 */
function makeValueOpts($code, $value, $default = "", $default_value = "0")
{
    // 一覧に設定
    $list = "";
    if ($default != "") {
        $list .= makeOptionTag($default_value,$default);
    }
    foreach ($value as $key => $val) {
        $tmp = makeOptionTag($key,$val,is_array($code) ? in_array($key,$code) : ($code == $key));
        $list .= $tmp;
    }
    return $list;
}

/**
 * 連想配列のリストを取得(グループ)
 *
 * @param $code
 * @param $valuelist
 * @param string $default
 * @param string $default_value
 * @return string
 */
function makeValueOptGroup($code, $valuelist, $default = "", $default_value = "0")
{
    // 一覧に設定
    $list = "";
    if ($default != "") {
        $list .= makeOptionTag($default_value,$default);
    }
    foreach ($valuelist as $list_key => $value) {
        $list .= '<optgroup label="' . $list_key . '">'."\n";
        foreach ($value as $key => $val) {
            $tmp = makeOptionTag($key,$val,is_array($code) ? in_array($key,$code) : ($code == $key));
            $list .= $tmp;
        }
        $list .= '</optgroup>'."\n";
    }
    return $list;
}

/**
 * 多次元連想配列のリストを取得
 *
 * @param $code
 * @param $value
 * @param $value_key
 * @param string $default
 * @param string $default_value
 * @return string
 */
function makeValuelistOpts($code, $value, $value_key, $default = "", $default_value = "0")
{
    // 一覧に設定
    $list = "";
    if ($default != "") {
        $list .= makeOptionTag($default_value,$default);
    }
    foreach ($value as $key => $val) {
        $tmp = makeOptionTag($key,$val[$value_key],is_array($code) ? in_array($key,$code) : ($code == $key));
        $list .= $tmp;
    }
    return $list;
}

/**
 * パンくずリストを生成
 *
 * @param $name
 * @param string $href
 * @return array
 */
function makePnkz($name, $href = "")
{
    return array("href" => $href, "value" => $name);
}

/**
 * @param $pnkz
 * @param array $options
 * @return string
 */
function getPnkz($pnkz, $options = array())
{
    $count = count($pnkz);

    $p = new class_pnkz();
    $start = "";
    if (isset($options["start"])) {
        $start = $options["start"];
    }
    $end = "";
    if (isset($options["end"])) {
        $end = $options["end"];
    }
    if($start || $end){
        $p->setWrapHTML($start,$end);
    }
    if (isset($options["mode"])) {
        $mode = $options["mode"];
        if($mode == 'list'){
            $p->setWrapElement('li');
        }else{
            $p->setWrapElement(null);
        }
    }
    foreach ($pnkz as $key => $val) {
        $p->addItem(new class_pnkzItem($val["value"],$val["href"]));
    }
    return $p->getHTML();
}

/**
 * 自動リンク
 *
 * @param $str
 * @param string $attr
 * @return mixed
 */
function autoURLLink($str, $attr = "")
{
    $patterns = "/(https?|ftp)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/i";
    if ($attr != "") {
        $attr = " " . $attr;
    }
    $replacements = "<a href=\"\\1\\2\"" . $attr . ">\\1\\2</a>";
    return preg_replace($patterns, $replacements, $str);
}

/**
 * @param $str
 * @param string $attr
 * @return mixed
 */
function autoTelLink($str, $attr = "")
{
    $patterns = "/(0\d{9,10})/i";
    if ($attr != "") {
        $attr = " " . $attr;
    }
    $replacements = "<a href=\"tel:\\1\"" . $attr . ">\\1</a>";
    return preg_replace($patterns, $replacements, $str);
}

/**
 * @param $str
 * @param string $attr
 * @return mixed
 */
function autoMailLink($str, $attr = "")
{
    $patterns = "/([a-zA-Z0-9_\.-]+\@)([a-zA-Z0-9_\.-]+)([a-zA-Z]+)/i";
    if ($attr != "") {
        $attr = " " . $attr;
    }
    $replacements = "<a href=\"mailto:\\1\\2\\3\"" . $attr . ">\\1\\2\\3</a>";
    return preg_replace($patterns, $replacements, $str);
}

/**
 * br→改行変換
 *
 * @param $string
 * @return mixed
 */
function br2nl($string)
{
    // 大文字・小文字を区別しない
    return preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/i', "\n", $string);
}

/**
 * @param $str
 * @param $start
 * @param $width
 * @param string $append
 * @return string
 */
function strimw($str,$start,$width,$append="..."){
    return mb_strimwidth($str,$start,$width,$append,SYSTEM_PHP_ENCODE);
}

/**
 * 画像タグ作成
 *
 * @param $url
 * @param int $w
 * @param int $h
 * @param string $alt
 * @return string
 */
function makeImgTag($url,$w=0,$h=0,$alt=""){
    $tag = '<img src="'.$url.'"';
    if($w != ""){
        $tag .= ' width="'.$w.'"';
    }
    if($h != ""){
        $tag .= ' height="'.$h.'"';
    }
    if($alt != ""){
        $tag .= ' alt="'.htmlspecialchars($alt).'"';
    }
    $tag .= '>';
    return $tag;
}

/**
 * チェックボックスリストを生成
 *
 * @param $value_key
 * @param $checklist
 * @param $valuelist
 * @param string $before
 * @param string $after
 * @return string
 */
function makeCheckboxList($value_key, $checklist, $valuelist, $before = "", $after = "")
{
    $list = "";
    if (count($valuelist) > 0) {
        foreach ($valuelist as $key => $value) {
            $checked = false;
            if (is_array($checklist) && in_array($key, $checklist)) {
                $checked = true;
            }
            $list .= $before . '<input type="checkbox" name="' . $value_key . '[]" id="' . $value_key . '_' . $key . '" value="' . $key . '" ' . htmlChecked($checked) . '> <label for="' . $value_key . '_' . $key . '">' . $value . '</label>' . $after;
        }
    }
    return $list;
}

