<?php

/**
 * template class
 * @copyright Copyright © 2014, ateliee
 * @author ateliee
 * @version 1.0
 */
class class_template
{
    var $Template;
    var $Include;
    var $Vars;
    var $TemplateList;
    var $left_delimiter = "<?";
    var $right_delimiter = "?>";
    var $html_Encoding;
    var $system_Encoding;

    function class_template()
    {
        $this->Template = "";
        $this->TemplateList = array();
        $this->Include = array();
        $this->clear_all_assign();
        $this->html_Encoding = mb_internal_encoding();
        $this->system_Encoding = mb_internal_encoding();
    }

    /**
     * set html encoding
     * @param string $encode encodetype(default internal encoding)
     * @return null
     */
    function sethtmlEncoding($encode)
    {
        $this->html_Encoding = $encode;
    }

    /**
     * set php encoding
     * @param string $encode encodetype(default internal encoding)
     * @return null
     */
    function setSystemEncoding($encode)
    {
        $this->system_Encoding = $encode;
    }

    /**
     * get set template variable
     * set $name is namespace for set variable
     * @param object $name
     * @return value
     */
    function get_template_var($name = null)
    {
        if (isset($name)) {
            return $this->Vars[$name];
        }
        return $this->Vars;
    }

    /**
     * set load html file
     * @param bool $filename (path)
     * @return bool
     */
    function load($filename)
    {
        $this->clear_all_assign();
        $this->Template = "";
        if (!($fp = @fopen($filename, 'rb'))) {
            return false;
        }
        $length = max(1000, filesize($filename));
        $this->Template = fread($fp, $length);
        fclose($fp);
        return true;
    }

    /**
     * set template and get html for string
     * @param bool $set
     * @return string
     */
    function get_display_template($set = true)
    {
        $template = $this->Template;
        if ($set) {
            // インクルード設定
            $template = $this->setInclude($template);

            // 変数設定
            $template = $this->setTemplatesVars($template);
        }
        return $template;
    }

    /**
     * set template and get html for string and print
     * @see get_display_template
     * @param bool $set
     * @return null
     */
    function display($set = true)
    {
        print $this->get_display_template($set);
    }

    /**
     * set template include file
     * @param string $id (namespace)
     * @param  string $filename
     * @return bool
     */
    function setIncludeFile($id, $filename)
    {
        if (!($fp = @fopen($filename, 'rb'))) {
            return false;
        }
        $length = max(1000, filesize($filename));
        $template = fread($fp, $length);
        fclose($fp);

        $this->Include[$id] = $template;
        return true;
    }

    /**
     * set template variable
     * @param string $id (namespace)
     * @return null
     */
    function assign($id, $val)
    {
        $this->Vars[$id] = $val;
    }

    /**
     * set template variable
     * @see assign
     * @param array $value
     * @return null
     */
    function assign_vars($value)
    {
        foreach ($value as $key => $val) {
            $this->assign($key, $val);
        }
    }

    /**
     * clear template variable
     * @return null
     */
    function clear_all_assign()
    {
        $this->Vars = array();
    }

    /**
     * get attribe
     * @return srting
     * @access private
     */
    function getAttr(&$attr, $str)
    {
        $attr = array();
        // key=valueで分割
        $preg_str = "/(\S+)=(\S+)/";
        if (preg_match_all($preg_str, $str, $matchs)) {
            $cnt = count($matchs[0]);
            for ($key = 0; $key < $cnt; $key++) {
                $name = strtoupper($matchs[1][$key]);
                $val = $matchs[2][$key];
                $val = $this->convertString($val);
                $attr[$name] = $val;
            }
        }
        return $attr;
    }

    /**
     * set template conditional expression
     * @return srting
     * @access private
     */
    function evaString($str)
    {
        $str = trim($str);
        // 条件式を取得
        $preg_str = "/^\s*?(\S+?)\s*?([\!\<\>\+\-\*\/%=]+)\s*?(\S+)\s*?$/";
        if (preg_match($preg_str, $str, $tp)) {
            $i1 = $this->convertString($tp[1]);
            $is = $tp[2];
            $i2 = $this->convertString($tp[3]);
            //if(!is_null($i1) && !is_null($i2)){
            switch ((string)$is) {
                case '===':
                    $item = ($i1 === $i2);
                    break;
                case '==':
                    $item = ($i1 == $i2);
                    break;
                case '<=':
                    $item = ($i1 <= $i2);
                    break;
                case '>=':
                    $item = ($i1 >= $i2);
                    break;
                case '<':
                    $item = ($i1 < $i2);
                    break;
                case '>':
                    $item = ($i1 > $i2);
                    break;
                case '!=':
                    $item = ($i1 != $i2);
                    break;
                case '+':
                    $item = ($i1 + $i2);
                    break;
                case '-':
                    $item = ($i1 - $i2);
                    break;
                case '*':
                    $item = ($i1 * $i2);
                    break;
                case '/':
                    $item = ($i1 / $i2);
                    break;
                case '%':
                    $item = ($i1 % $i2);
                    break;
            }
            //}
        } else {
            $item = $this->convertString($str);
        }
        return $item;
    }

    /**
     * set template function
     * @return srting
     * @access private
     */
    function convertString($str, $encode = true)
    {
        // 関数
        $check = true;
        if (preg_match("/^([a-zA-Z][a-zA-Z0-9_]+)\(([\s\S]*)\)$/", $str, $matchs)) {
            $check = false;
            $val = $this->convertString($matchs[2], false);
            switch ($matchs[1]) {
                case 'is_array':
                    $str = is_array($val);
                    break;
                case 'is_numeric':
                    $str = is_numeric($val);
                    break;
                case 'is_string':
                    $str = is_string($val);
                    break;
                // escape
                case 'escape':
                    $str = htmlspecialchars($val, ENT_QUOTES, $this->system_Encoding);
                    break;
                case 'htmlentities':
                    $str = htmlentities($val, ENT_COMPAT, $this->system_Encoding);
                    break;
                case 'htmlspecialchars':
                    $str = htmlspecialchars($val, ENT_COMPAT, $this->system_Encoding);
                    break;
                case 'escape_br':
                    $str = htmlentities($val, ENT_COMPAT, $this->system_Encoding);
                    $str = nl2br($str);
                    break;
                case 'quotes':
                    $str = preg_replace("/\"/", "\\\"", $val);
                    break;
                case 'urlencode':
                    $str = urlencode($val);
                    break;
                // format
                case 'number_format':
                    $str = number_format($val);
                    break;
                case 'count':
                    $str = count($val);
                    break;
                default:
                    $check = true;
                    break;
            }
        }
        $result = $str;
        if ($check) {
            // 文字列
            if (preg_match("/^\"([\s\S]*)\"$/", $str, $matchs)) {
                $result = (string)$matchs[1];
            } elseif (preg_match("/^'([\s\S]*)'$/", $str, $matchs)) {
                $result = (string)$matchs[1];
                // 配列
            } elseif (preg_match("/^\\\$([\[\]_a-zA-Z0-9\.\\\$]+)$/", $str, $matchs)) {
                // ,で分割
                $vlist = explode(".", $matchs[1]);
                foreach ($vlist as $v) {
                    // 内部
                    if (preg_match("/^([_a-zA-Z0-9]+)(\[([\\\$_a-zA-Z0-9]+)\])?$/", $v, $m)) {
                        $key = $m[1];
                        if (isset($value)) {
                            if (!isset($value[$key])) {
                                if (!array_key_exists($key, $value)) {
                                    trigger_error("template : not found [" . $matchs[1] . "] value;<br />", E_USER_WARNING);
                                }
                                $value = NULL;
                            } else {
                                $value = $value[$key];
                            }
                        } else {
                            $value = $this->Vars[$key];
                        }
                        if (isset($m[3])) {
                            $key = $this->convertString($m[3]);
                            $value = $value[$key];
                        }
                    }
                }
                $result = $value;
            } elseif (is_numeric($str)) {
                $result = intval($str);
            } elseif (strtoupper($str) == "TRUE") {
                $result = true;
            } elseif (strtoupper($str) == "FALSE") {
                $result = false;
            } elseif (strtoupper($str) == "NULL") {
                $result = NULL;
            }
        }
        if (is_string($result) && $encode && ($this->html_Encoding != $this->system_Encoding)) {
            $result = mb_convert_encoding($result, $this->html_Encoding, $this->system_Encoding);
        }
        return $result;
        //return $this->left_delimiter.$str.$this->right_delimiter;
    }

    /**
     * set template include
     * @return srting $template
     * @access private
     */
    function setInclude($template)
    {
        //$preg_str = "/([\s\S]*?)".preg_quote($this->left_delimiter)."INCLUDE\s+([\s\S]+?)".preg_quote($this->right_delimiter)."([\s\S]*?)/i";
        $preg_str = "/" . preg_quote($this->left_delimiter, "/") . "INCLUDE\s+([\s\S]+?)" . preg_quote($this->right_delimiter, "/") . "/i";
        $template = preg_replace_callback($preg_str, array($this, '_setIncludeCallback'), $template);
        return $template;
    }

    function _setIncludeCallback($args)
    {
        //$tp_header = $args[1];
        //$var = $args[2];
        //$tp_footer = $args[3];
        $var = $args[1];
        // 属性値を取得
        $attr = $this->getAttr($attr, $var);
        // 属性から値設定
        $tmp = "";
        foreach ($attr as $name => $val) {
            switch ($name) {
                case "FILE":
                    $tmp = $this->Include[$val];
                    break;
            }
        }
        //$template = $tp_header.$tmp.$tp_footer;
        //return $template;
        return $tmp;
    }

    /**
     * set template value
     * @return srting $template
     * @access private
     */
    function setTemplatesVars($template)
    {
        $preg_str = "/" . preg_quote($this->left_delimiter, "/") . "([\s\S]+?)" . preg_quote($this->right_delimiter, "/") . "/";
        // 文字列の分割
        $matchs = preg_split($preg_str, $template, 0, PREG_SPLIT_DELIM_CAPTURE);
        $this->TemplateList = $matchs;
        $cnt = count($matchs);
        // テンプレートを評価
        $tmp = $matchs[0];
        $level = 0;
        for ($key = 1; $key < $cnt; $key += 2) {
            $this->_setTemplateTags($tmp, $key, $matchs, $level);
        }
        return $tmp;
    }

    function _setTemplateTags(&$tmp, &$key, &$list, &$level, $check = false, $skip = false)
    {
        // 式を取得
        $ptn = $list[$key];
        // foreach
        if (preg_match("/^(\/?)FOREACH\s*([\s\S]+?)$/i", $ptn, $m)) {
            // 終了タグ
            if ($m[1] == "/") {
                $level--;
                // 開始タグ
            } else {
                $level++;
                if ($skip == false) {
                    // ループ処理
                    $this->_setForeachLoop($m[2], $tmp, $key, $list, $level);
                } else {
                    // タグスキップ
                    $this->_skipIfTags($tmp, $key, $list, $level);
                }
            }
            // for文
        } else if (preg_match("/^(\/?)FOR\s*([\s\S]+?)$/i", $ptn, $m)) {
            // 終了タグ
            if ($m[1] == "/") {
                $level--;
                // 開始タグ
            } else {
                $level++;
                if ($skip == false) {
                    // ループ処理
                    $this->_setForLoop($m[2], $tmp, $key, $list, $level);
                } else {
                    // タグスキップ
                    $this->_skipIfTags($tmp, $key, $list, $level);
                }
            }
            // if文
        } else if (preg_match("/^IF\s*\(\s*([\s\S]+?)\)$/i", $ptn, $m)) {
            $level++;
            if ($skip == false) {
                // IF処理
                $this->_setIf($m[1], $tmp, $key, $list, $level);
            } else {
                // タグスキップ
                $this->_skipIfTags($tmp, $key, $list, $level);
            }
            // if文終了
        } else if (preg_match("/^\/IF$/i", $ptn, $m)) {
            $level--;
            // 無効な値は無視
        } else {
            if ($skip == false) {
                $var = $this->evaString($ptn);
                $tmp .= $var;
                $tmp .= $list[$key + 1];
            }
        }
        return $check;
    }

    function _skipIfTags(&$tmp, &$key, &$list, &$level)
    {
        $cnt = count($list);
        $start_level = $level - 1;
        for ($key += 2; $key < $cnt; $key += 2) {
            // タグの実装
            $this->_setTemplateTags($tmp, $key, $list, $level, true, true);
            // レベルチェック
            if ($level <= $start_level) {
                //echo str_repeat("　",$level).htmlspecialchars($list[$key])."A<br/>";
                break;
            }
        }
    }

    /**
     * set template FOR
     * @access private
     */
    function _setForLoop($str, &$tmp, &$key, &$list, &$level)
    {
        // 要素を抽出
        preg_match("/^\s*\\\$([\S]+)=([\S]+)\s+TO\s+(\S+)\s*((\S+)\s*)?$/i", $str, $m);
        $name = $m[1];
        $start = $this->convertString($m[2]);
        $loop = $this->convertString($m[3]);
        $step = 1;
        if (isset($m[5])) {
            $at = $m[5];
            // 属性値を取得
            $attr = $this->getAttr($attr, $at);
            if (isset($attr["STEP"])) {
                $step = intval($attr["STEP"]);
            }
        }
        $start_key = $key;
        $start_level = $level - 1;
        $cnt = count($list);
        if ($start < $loop) {
            for ($i = $start; $i <= $loop; $i += $step) {
                $this->assign($name, $i);
                $tmp .= $list[$key + 1];
                $key += 2;
                while ($key < $cnt) {
                    // タグの実装
                    $this->_setTemplateTags($tmp, $key, $list, $level);
                    // レベルチェック
                    if ($level <= $start_level) {
                        if (($i + $step) <= $loop) {
                            // キー値を戻す
                            $key = $start_key;
                            $level++;
                        }
                        break;
                    }
                    $key += 2;
                };
            }
            $tmp .= $list[$key + 1];
        } else {
            // タグスキップ
            $this->_skipIfTags($tmp, $key, $list, $level);
            $tmp .= $list[$key + 1];
        }
    }

    /**
     * set template FOREACH
     * @access private
     */
    function _setForeachLoop($str, &$tmp, &$key, &$list, &$level)
    {
        // 要素を抽出
        preg_match("/^\s*([\S]+)\s+AS\s+([\S\s]+?)\s*$/i", $str, $m);
        $item_list = $this->convertString($m[1]);
        $loop_key = "";
        $name = "";
        $s = $m[2];
        if (preg_match("/^\\\$([\S]+)\s*=>\s*\\\$([\S]+)$/", $s, $m)) {
            $loop_key = $m[1];
            $name = $m[2];
        } elseif (preg_match("/^\\\$([\S]+)$/", $s, $m)) {
            $name = $m[1];
        }
        // セクションの設定
        $start_key = $key;
        $start_level = $level - 1;
        $cnt = count($list);
        $item_cnt = count($item_list);
        if (0 < $item_cnt) {
            $i = 0;
            foreach ($item_list as $item_key => $item) {
                if ($loop_key != "") {
                    $this->assign($loop_key, $item_key);
                }
                $this->assign($name, $item);

                $tmp .= $list[$key + 1];
                $key += 2;
                while ($key < $cnt) {
                    // タグの実装
                    $this->_setTemplateTags($tmp, $key, $list, $level);
                    // レベルチェック
                    if ($level <= $start_level) {
                        if (($i + 1) < $item_cnt) {
                            // キー値を戻す
                            $key = $start_key;
                            $level++;
                        }
                        break;
                    }
                    $key += 2;
                };
                $i++;
            }
            $tmp .= $list[$key + 1];
        } else {
            // タグスキップ
            $this->_skipIfTags($tmp, $key, $list, $level);
            $tmp .= $list[$key + 1];
        }
    }

    /**
     * set template IF
     * @access private
     */
    function _setIf($str, &$tmp, &$key, &$list, &$level)
    {
        // if処理
        $check = $this->_setIfExecute($str, $tmp, $key, $list, $level);

        $start_level = $level - 1;
        $cnt = count($list);
        $loop = 0;
        for (; $key < $cnt; $key += 2) {
            //echo str_repeat("　",$level).htmlspecialchars($list[$key])."A<br/>";
            // 式を取得
            $ptn = $list[$key];
            // elseif文
            if (preg_match("/^(\/?)ELSE\s*IF\s*\(\s*([\s\S]+?)\)$/i", $ptn, $m)) {
                //echo "skip";
                if ($check == false) {
                    // if文法処理
                    if ($check = $this->_setIfExecute($m[2], $tmp, $key, $list, $level, $check)) {
                    }
                    $key -= 2;
                    continue;
                } else {
                    // タグスキップ
                    $this->_skipIfTags($tmp, $key, $list, $level);
                }
                // else文
            } else if (preg_match("/^(\/?)ELSE\s*$/i", $ptn, $m)) {
                if ($check == false) {
                    // if文法処理
                    if ($check = $this->_setIfExecute(NULL, $tmp, $key, $list, $level, $check)) {
                    }
                    $key -= 2;
                    continue;
                } else {
                    // タグスキップ
                    $this->_skipIfTags($tmp, $key, $list, $level);
                }
            } else {
                $this->_setTemplateTags($tmp, $key, $list, $level, $check, ($check == false));
            }
            // レベルチェック
            if ($level <= $start_level) {
                $tmp .= $list[$key + 1];
                break;
            }
            $loop++;
        }
    }

    /**
     * set template IF/ELSEIF/ELSE
     * @access private
     */
    function _setIfExecute($str, &$tmp, &$key, &$list, &$level, $checked = false)
    {
        // チェック
        $check = true;
        if ($str != NULL) {
            $check = $this->_setIfCheck($str);
        }
        if ($check && $checked == false) {
            $tmp .= $list[$key + 1];
        }
        $key += 2;
        return ($check | $checked);
    }

    function _setIfCheck($str)
    {
        $check = false;
        if ($this->evaString($str)) {
            $check = true;
        }
        return $check;
    }
}
