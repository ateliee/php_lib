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

    /**
     * @param $dir
     * @param $file
     * @return string
     */
    static function path_combine($dir, $file){
        return rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * @param $data
     * @param string $default_encoding
     * @param string $output_encoding
     */
    public function outputJson($data,$default_encoding='UTF-8',$output_encoding='UTF-8'){
        if($output_encoding != $output_encoding){
            $data = recursive_mb_convert_encoding($data,$output_encoding,$default_encoding);
        }
        header("Content-type: text/html; charset=".$output_encoding."\n\n");
        print json_encode($data);
        exit;
    }

    /**
     * @param $url
     * @return mixed|null
     */
    public function getUrlImageData($url){
        if($ch = curl_init()){
            curl_setopt ($ch,CURLOPT_URL,$url);
            curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch,CURLOPT_FOLLOWLOCATION,true);
            curl_setopt ($ch,CURLOPT_MAXREDIRS,10);
            curl_setopt ($ch,CURLOPT_AUTOREFERER,true);
            $data = curl_exec($ch);
            curl_close ($ch);
            return $data;
        }
        return null;
    }
}