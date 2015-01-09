<?php

/**
 * Class class_formColumn
 */
class class_formColumn
{
    static $CONV_FLOAT = 'f';
    static $CONV_INT = 'i';
    static $CONV_N = 'n';
    static $CONV_TRIM = 't';
    static $CONV_ALPHABET = 'a';
    static $CONV_KV = 'KV';

    static $CHECK_EMPTY = 'e';
    static $CHECK_ISINIT = 'i';
    static $CHECK_ALPHABET = 'a';
    static $CHECK_MAIL = 'm';
    static $CHECK_URL = 'u';
    static $CHECK_FORMAT = '';

    private $name;
    private $default;
    private $max;
    private $min;
    private $check;
    private $field;
    private $format;

    function __construct($data)
    {
        if(is_array($data)){
            if(isset($data['name'])){
                $this->setName($data['name']);
            }
            if(isset($data['default'])){
                $this->setDefault($data['default']);
            }
            if(isset($data['field'])) {
                $this->setField($data['field']);
            }
            if(isset($data['format'])) {
                $this->setFormat($data['format']);
            }
            if(isset($data['check'])) {
                $this->setCheck($data['check']);
            }
            if(isset($data['min'])){
                $this->setMin($data['min']);
            }
            if(isset($data['max'])){
                $this->setMax($data['max']);
            }
        }else{
            throw new Exception('column __construct type not support. array give.');
        }
    }

    /**
     * @param $value
     * @return array
     */
    public function getErrorKeys($value){
        $errors = array();
        if(preg_match("/".self::$CHECK_EMPTY."/",$this->check)){
            if((isset($value) == false) || ($value == "")){
                $errors[] = 'empty';
            }
        }
        if(preg_match("/".self::$CHECK_ISINIT."/",$this->check)){
            if(($value != "") && is_numeric($value) == false){
                $errors[] = 'int';
            }
        }
        if(preg_match("/".self::$CHECK_ALPHABET."/",$this->check)){
            if(($value != "") && preg_match("/^([0-9a-zA-Z\.]+)$/",$value) == false){
                $errors[] = 'alphabet';
            }
        }
        if(preg_match("/".self::$CHECK_MAIL."/",$this->check)){
            if(($value != "") && checkMail($value) == false){
                $errors[] = 'mail';
            }
        }
        if(preg_match("/".self::$CHECK_URL."/",$this->check)){
            if(($value != "") && checkURL($value) == false){
                $errors[] = 'mail';
            }
        }
        if($this->format){
            if(($value != "") && !preg_match("/".$this->format."/",$value)){
                $errors[] = 'format';
            }
        }
        return $errors;
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @param mixed $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @param mixed $field
     */
    public function addField($field)
    {
        $this->field .= $field;
    }

    /**
     * @param mixed $format
     */
    public function deleteField($field)
    {
        $this->field = str_replace($field,'',$this->field);
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return mixed
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * @param mixed $check
     */
    public function setCheck($check)
    {
        $this->check = $check;
    }

    /**
     * @param $value
     * @return float|int|string
     */
    public function convertValue($value)
    {
        if(is_array($value)){
            foreach($value as $key => $val){
                $value[$key] = $this->convertValue($val);
            }
        }else {
            $value = html_entity_decode($value,ENT_QUOTES,mb_internal_encoding());
            if (preg_match("/f/", $this->field)) {
                $value = floatval($value);
            }
            if (preg_match("/i/", $this->field)) {
                $value = intval($value);
            }
            if (preg_match("/n/", $this->field)) {
                $value = mb_convert_kana($value, "n", mb_internal_encoding());
            }
            if (preg_match("/t/", $this->field)) {
                $value = trim($value);
            }
            if (preg_match("/a/", $this->field)) {
                $value = mb_convert_kana($value, "a", mb_internal_encoding());
            }
            if (preg_match("/KV/", $this->field)) {
                $value = mb_convert_kana($value, "KV", mb_internal_encoding());
            }
            if (isset($this->min)) {
                $value = max($value, $this->min);
            }
            if (isset($this->max)) {
                $value = min($value, $this->max);
            }
        }
        return $value;
    }
}

/**
 * Class class_form
 */
class class_form
{
    private $columns;
    private $values;
    private $errorfunc;
    private $errors;

    /**
     *
     */
    function __construct()
    {
        $this->columns = array();
        $this->values = array();
        $this->errorfunc = array($this,'getError');
        $this->errors = array();
        if(func_num_args() > 0){
            $this->setColumns(func_get_arg(0));
        }
    }

    /**
     * @param $columns
     * @throws Exception
     */
    public function setColumns($columns)
    {
        $this->columns = array();
        foreach($columns as $key => $val){
            if(is_array($val)){
                $c = new class_formColumn($val);
                $this->addColumn($key,$c);
            }else if($val instanceof class_formColumn){
                $this->addColumn($key,$val);
            }else{
                throw new Exception('column type not support.');
            }
        }
        $this->setDefaultValues();
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     *
     */
    private function setDefaultValues()
    {
        $this->values = array();
        foreach($this->columns as $key => $val){
            $this->setDefaultValue($key);
        }
    }

    /**
     * @param $key
     */
    private function setDefaultValue($key)
    {
        $field = $this->getColumn($key);
        $value = $field->getDefault();
        $this->values[$key] = $value;
    }

    /**
     * @param $key
     * @param class_formColumn $column
     */
    public function addColumn($key,class_formColumn $column)
    {
        $this->columns[$key] = $column;
        $this->setDefaultValue($key);
    }

    /**
     * @return class_formColumn
     */
    public function getColumn($key)
    {
        return $this->columns[$key];
    }

    /**
     * @param $postvalue
     * @return array
     */
    public function bindRequest($postvalue,$prex='')
    {
        $values = array();
        foreach($this->columns as $key => $val){
            $field = $this->getColumn($key);
            $value = $field->getDefault();
            if(isset($postvalue[$prex.$key])){
                $value = $field->convertValue($postvalue[$prex.$key]);
            }
            $values[$key] = $value;
        }
        $this->values = $values;
        $this->checkErrors();

        return $values;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return (count($this->errors) ? true : false);
    }

    /**
     * @param null $callback
     * @return array
     */
    public function getErrorsSingleArray($callback=null)
    {
        $errors = $this->getErrors($callback);
        $err = array();
        foreach($errors as $key => $val){
            $err[$key] = $val[0];
        }
        return $err;
    }

    /**
     * @param null $callback
     * @return array
     */
    public function getErrors($callback=null)
    {
        if($callback){
            $this->errorfunc = $callback;
        }
        $errors = array();
        foreach($this->errors as $key => $val){
            $e = array();
            foreach($val as $k => $v){
                $e[] = call_user_func($this->errorfunc,$v,$this->getColumn($key),$this->values[$key]);
            }
            $errors[$key] = $e;
        }
        return $errors;
    }

    /**
     * @return bool
     */
    public function checkErrors()
    {
        $this->errors = array();
        foreach($this->columns as $key => $val){
            $column = $this->getColumn($key);
            $e = array();
            if(is_array($this->values[$key])){
                foreach($this->values[$key] as $k => $v){
                    $e = $column->getErrorKeys($v);
                    if(count($e) > 0){
                        break;
                    }
                }
            }else{
                $e = $column->getErrorKeys($this->values[$key]);
            }
            if(count($e) > 0){
                $this->errors[$key] = $e;
            }
        }
        if(count($this->errors) > 0){
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @param class_formColumn $column
     * @param $value
     * @return string
     */
    public function getError($key,class_formColumn $column,$value)
    {
        $message = "";
        switch($key){
            case "empty";
                $message = $column->getName()."が入力されていません。";
                break;
            case "int";
                $message = $column->getName()."が数値ではありません。";
                break;
            case "alphabet";
                $message = $column->getName()."が英数字ではありません。";
                break;
            case "mail";
                $message = $column->getName()."が間違っています。";
                break;
            case "url";
                $message = $column->getName()."が間違っています。";
                break;
            case "format";
            default;
                $message = $column->getName()."が間違っています。";
                break;
        }
        return $message;
    }
}
