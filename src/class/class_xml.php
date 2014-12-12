<?php
//============================================
// class_xml.php
//============================================
//+++++++++++++++++++++++++++++
// XMLクラス
//+++++++++++++++++++++++++++++
class class_xml
{
    var $option_indent = "\t";
    var $option_version = '1.0';
    var $option_encoding = 'UTF-8';
    var $option_rootName = 'result';
    var $option_rootAttributes = array();
    var $option_defaulTagName = 'item';

//-----------------------------
// 連想配列をXMLに変換
//-----------------------------
    function serialize($xml, $options)
    {
        // 属性値を設定
        $root_name = $this->option_rootName;
        $root_attr = $this->option_rootAttributes;
        if (isset($options['rootName'])) {
            $root_name = $options['rootName'];
        }
        if (isset($options['rootAttributes'])) {
            if (is_array($options['rootAttributes'])) {
                $root_attr = $options['rootAttributes'];
            }
        }

        // XML作成
        $result = '';
        $result .= '<?xml version="' . $this->option_version . '" encoding="' . $this->option_encoding . '"?>' . "\n";
        $result .= $this->_serialize($xml, $root_name, $root_attr) . "\n";

        return $result;
    }
//-----------------------------
// XMLを連想配列に変換
//-----------------------------
    function unserialize($xml)
    {
        $result = array();
        return $result;
    }
//-----------------------------
// 内部関数
//-----------------------------
    function _serialize($item, $item_key, $attributes = array(), $h = 0)
    {
        $result = '';
        if (is_array($item)) {
            $result = $this->_serializeArray($item, $item_key, $attributes, $h);
        } else {
            $result = $this->_serializeValue($item, $item_key, $attributes, $h);
        }
        return $result;
    }

    function _serializeAttributes($attributes)
    {
        // 属性値を設定
        $attr = '';
        foreach ($attributes as $key => $val) {
            $attr .= ' ' . $key . '="' . $val . '"';
        }
        return $attr;
    }

    function _serializeArray($item, $item_key, $attributes, $h)
    {
        $result = '';

        $indent = str_repeat($this->option_indent, $h);
        $indexed = true;
        foreach ($item as $key => $val) {
            if (!is_int($key)) {
                $indexed = false;
                break;
            }
        }
        if ($indexed) {
            foreach ($item as $key => $val) {
                $attr = array();
                if (isset($val['_attributes']) && is_array($val['_attributes'])) {
                    $attr = $val['_attributes'];
                    unset($val['_attributes']);
                }
                $result .= $this->_serialize($val, $item_key, $attr, $h) . "\n";
            }
        } else {
            $result .= $indent . '<' . $item_key . $this->_serializeAttributes($attributes) . '>' . "\n";
            foreach ($item as $key => $val) {
                $attr = array();
                if (isset($val['_attributes']) && is_array($val['_attributes'])) {
                    $attr = $val['_attributes'];
                    unset($val['_attributes']);
                }
                $result .= $this->_serialize($val, $key, $attr, $h + 1) . "\n";
            }
            $result .= $indent . '</' . $item_key . '>';
        }
        return $result;
    }

    function _serializeValue($item, $item_key, $attributes, $h)
    {
        $result = '';
        $indent = str_repeat($this->option_indent, $h);

        $result .= $indent . '<' . $item_key . $this->_serializeAttributes($attributes) . '>';
        $result .= $item;
        $result .= '</' . $item_key . '>';
        return $result;
    }
}
