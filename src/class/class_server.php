<?php

/**
 * Class class_server
 */
class class_server
{
    /**
     * @param $status_code
     * @return bool
     */
    static function headerStatus($status_code){
        if($status_code == 301){
            header("HTTP/1.1 301 Moved Permanently");
        }else if($status_code == 404){
            header("HTTP/1.0 404 Not Found");
        }else if($status_code == 410){
            header("HTTP/1.1 410 Gone");
        }else{
            return false;
        }
        return true;
    }

    /**
     * @param array $array
     */
    static function addHeaders($array=array()){
        foreach($array as $key => $val){
            header($key.": ".$val."\n\n");
        }
    }

    /**
     * @param $url
     * @param int $status_code
     */
    static function redirect($url,$status_code=0){
        if($status_code > 0){
            self::headerStatus($status_code);
        }
        // リダイレクト
        header('Location: '.$url);
        exit;
    }
}