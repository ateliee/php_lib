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
    private $maxc;
    private $min;
    private $minc;
    private $range_max;
    private $range_maxc;
    private $range_min;
    private $range_minc;
    private $check;
    private $field;
    private $format;

    function __construct($data)
    {
        if(is_array($data)){
            foreach($data as $key => $val){
                if($key == 'name') {
                    $this->setName($val);
                }else if($key == 'default'){
                    $this->setDefault($val);
                }else if($key == 'field'){
                    $this->setField($val);
                }else if($key == 'format'){
                    $this->setFormat($val);
                }else if($key == 'check'){
                    $this->setCheck($val);
                }else if($key == 'min'){
                    $this->setMin($val);
                }else if($key == 'max'){
                    $this->setMax($val);
                }else if($key == 'range_min'){
                    $this->setRangeMin($val);
                }else if($key == 'range_max'){
                    $this->setRangeMax($val);
                }else{
                    trigger_error('class_form : un support data key "'.$key.'"',E_USER_NOTICE);
                }
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
        if($value != ""){
            if($this->maxc){
                if($value > $this->max){
                    $errors[] = 'max';
                }
            }
            if($this->minc){
                if($value < $this->min){
                    $errors[] = 'min';
                }
            }
            if($this->range_maxc){
                if(mb_strlen($value) > $this->range_max){
                    $errors[] = 'range_max';
                }
            }
            if($this->range_minc){
                if(mb_strlen($value) < $this->range_min){
                    $errors[] = 'range_min';
                }
            }
            if($this->format){
                if(!preg_match("/".$this->format."/",$value)){
                    $errors[] = 'format';
                }
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
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
    }


    /**
     * @param mixed $max
     */
    public function setMax($max)
    {
        $this->max = $max;
        $this->maxc = true;
    }

    /**
     * @return mixed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param mixed $min
     */
    public function setMin($min)
    {
        $this->min = $min;
        $this->minc = true;
    }

    /**
     * @return mixed
     */
    public function getRangeMax()
    {
        return $this->range_max;
    }

    /**
     * @param mixed $range_max
     */
    public function setRangeMax($range_max)
    {
        $this->range_max = $range_max;
        $this->range_maxc = true;
    }

    /**
     * @return mixed
     */
    public function getRangeMin()
    {
        return $this->range_min;
    }

    /**
     * @param mixed $range_min
     */
    public function setRangeMin($range_min)
    {
        $this->range_min = $range_min;
        $this->range_minc = true;
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
     * @param mixed $check
     */
    public function addCheck($check)
    {
        $this->check .= $check;
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
        }
        return $value;
    }
}

/**
 * Class class_form
 */
class class_form
{
    private $form_name;
    private $columns;
    private $values;
    private $errorfunc;
    private $errors;

    /**
     *
     */
    function __construct()
    {
        $this->form_name = null;
        $this->columns = array();
        $this->values = array();
        $this->errorfunc = array($this,'getError');
        $this->errors = array();
        if(func_num_args() > 1) {
            $this->form_name = func_get_arg(0);
            $this->setColumns(func_get_arg(1));
        }else if(func_num_args() > 0){
            $this->setColumns(func_get_arg(0));
        }
    }

    /**
     * @param mixed $form_name
     */
    public function setFormName($form_name)
    {
        $this->form_name = $form_name;
    }

    /**
     * @return mixed
     */
    public function getFormName(){
        return $this->form_name;
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
     * @param $key
     * @param null $default
     * @return null
     */
    public function getValue($key,$default=null)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $default;
    }

    /**
     * @param $key
     */
    public function setValue($key,$value)
    {
        $this->values[$key] = $value;
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
     * @return class_formColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
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

        return $values;
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
    public function isValid()
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
            return false;
        }
        return true;
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
            case "min";
                $message = $column->getName()."は".$column->getMin()."以上の数字を入力して下さい。";
                break;
            case "max";
                $message = $column->getName()."は".$column->getMax()."以下の数字を入力して下さい。";
                break;
            case "range_min";
                $message = $column->getName()."は".$column->getRangeMin()."文字以上で入力して下さい。";
                break;
            case "range_max";
                $message = $column->getName()."は".$column->getRangeMax()."文字以下で入力して下さい。";
                break;
            case "format";
            default;
                $message = $column->getName()."が間違っています。";
                break;
        }
        return $message;
    }
}
