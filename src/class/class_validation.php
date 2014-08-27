<?php
/**
 * Class class_validation
 */
class class_validation
{
    private $callback;
    private $prex;

    /**
     *
     */
    function __construct() {
        $this->prex = "";
        $this->callback = null;
    }
    /**
     * @param $func
     */
    public function messageCallback($func){
        $this->callback = $func;
    }

    /**
     * @return array|null
     */
    protected function getMessageCallback(){
        $callback = array(this,"errorMessage");
        if(!$this->callback){
            $callback = $this->callback;
        }
        return $callback;
    }

    /**
     * @param $postvalue
     * @param $fields 項目チェック(e:空か、i:数値、a:アルファベット、m:メールアドレス、u:URL)
     * @param $errors 返還するエラーメッセージ
     * @param null $check_key チェックするキー
     * @return array
     */
    public function run($postvalue,$fields,&$errors,$check_key = NULL){
        $errors = array();
        $callback = $this->getMessageCallback();
        foreach($fields as $key => $field){
            if($check_key && is_array($check_key)){
                if(!in_array($key,$check_key)){
                    continue;
                }
            }
            $e = "";
            if(is_array($postvalue[$key])){
                foreach($postvalue[$key] as $k => $v){
                    $e = $this->checkValue($v,$field["check"],$field["name"],$callback);
                    if(($e == "") && isset($field["format"])){
                        if(($v != "") && !preg_match("/".$field["format"]."/",$v)){
                            $e = $callback("format",$field["name"],$v);
                        }
                    }
                    if($e != ""){
                        break;
                    }
                }
            }else{
                $e = $this->checkValue($postvalue[$key],$field["check"],$field["name"],$callback);
                if(($e == "") && isset($field["format"])){
                    if(($postvalue[$key] != "") && !preg_match("/".$field["format"]."/",$postvalue[$key])){
                        $e = $callback("format",$field["name"],$postvalue[$key]);
                    }
                }
            }
            if($e != ""){
                $errors[$this->prex.$key] = $e."\n";
            }
        }
        return $errors;
    }

    /**
     * @param $value
     * @param $field_check
     * @param $field_name
     * @return string
     */
    protected function checkValue($value,$field_check,$field_name){
        $callback = $this->getMessageCallback();
        $e = "";
        if(($e == "") && preg_match("/e/",$field_check)){
            if((isset($value) == false) || ($value == "")){
                $e = $callback("empty",$field_name,$value);
            }
        }
        if(($e == "") && preg_match("/i/",$field_check)){
            if(($value != "") && is_numeric($value) == false){
                $e = $callback("int",$field_name,$value);
            }
        }
        if(($e == "") && preg_match("/a/",$field_check)){
            if(($value != "") && preg_match("/^([0-9a-zA-Z\.]+)$/",$value) == false){
                $e = $callback("alphabet",$field_name,$value);
            }
        }
        if(($e == "") && preg_match("/m/",$field_check)){
            if(($value != "") && checkMail($value) == false){
                $e = $callback("mail",$field_name,$value);
            }
        }
        if(($e == "") && preg_match("/u/",$field_check)){
            if(($value != "") && checkURL($value) == false){
                $e = $callback("url",$field_name,$value);
            }
        }
        return $e;
    }

    /**
     * @param $key
     * @param $name
     * @param $value
     * @return string
     */
    protected function errorMessage($key,$name,$value){
        $message = "";
        switch($key){
            case "empty";
                $message = $name."が入力されていません。";
                break;
            case "int";
                $message = $name."が数値ではありません。";
                break;
            case "alphabet";
                $message = $name."が英数字ではありません。";
                break;
            case "mail";
                $message = $name."が間違っています。";
                break;
            case "url";
                $message = $name."が間違っています。";
                break;
            case "format";
            default;
                $message = $name."が間違っています。";
                break;
        }
        return $message;
    }
}