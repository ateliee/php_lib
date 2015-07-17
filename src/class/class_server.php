<?php

/**
 * Class class_server
 */
class class_server
{

    static $MIME_TYPE = array(
        // テキスト・文書・MSオフィス関連
        array('MIME' => 'text/plain', 'EXT' => 'txt'),
        array('MIME' => 'text/csv', 'EXT' => 'csv'),
        array('MIME' => 'text/tab-separated-values', 'EXT' => 'tsv'),
        array('MIME' => 'application/msword', 'EXT' => 'doc'),
        array('MIME' => 'application/msword', 'EXT' => 'docx'),
        array('MIME' => 'application/vnd.ms-excel', 'EXT' => 'xls'),
        array('MIME' => 'application/vnd.ms-excel', 'EXT' => 'xlsx'),
        array('MIME' => 'application/vnd.ms-powerpoint', 'EXT' => 'ppt'),
        array('MIME' => 'application/pdf', 'EXT' => 'pdf'),
        array('MIME' => 'application/vnd.fujixerox.docuworks', 'EXT' => 'xdw'),
        array('MIME' => 'text/html', 'EXT' => 'htm'),
        array('MIME' => 'text/html', 'EXT' => 'html'),
        array('MIME' => 'text/css', 'EXT' => 'css'),
        array('MIME' => 'text/javascript', 'EXT' => 'js'),
        array('MIME' => 'text/x-hdml', 'EXT' => 'hdml'),
        // 画像関連
        array('MIME' => 'image/jpeg', 'EXT' => 'jpg'),
        array('MIME' => 'image/jpeg', 'EXT' => 'jpeg'),
        array('MIME' => 'image/pjpeg', 'EXT' => 'jpeg'),
        array('MIME' => 'image/png', 'EXT' => 'png'),
        array('MIME' => 'image/gif', 'EXT' => 'gif'),
        array('MIME' => 'image/bmp', 'EXT' => 'bmp'),
        array('MIME' => 'application/postscript', 'EXT' => 'ai'),
        // 音声関連
        array('MIME' => 'audio/mpeg', 'EXT' => 'mp3'),
        array('MIME' => 'audio/mp4', 'EXT' => 'mp4'),
        array('MIME' => 'audio//x-wav', 'EXT' => 'wav'),
        array('MIME' => 'audio/midi', 'EXT' => 'mid'),
        array('MIME' => 'audio/midi', 'EXT' => 'midi'),
        array('MIME' => 'application/x-smaf', 'EXT' => 'mmf'),
        // 動画関連
        array('MIME' => 'video/mpeg', 'EXT' => 'mpg'),
        array('MIME' => 'video/mpeg', 'EXT' => 'mpeg'),
        array('MIME' => 'video/x-ms-wmv', 'EXT' => 'wmv'),
        array('MIME' => 'application/x-shockwave-flash', 'EXT' => 'swf'),
        array('MIME' => 'video/3gpp2', 'EXT' => '3g2'),
        // アプリケーション関連
        array('MIME' => 'application/zip', 'EXT' => 'zip'),
        array('MIME' => 'application/x-lzh', 'EXT' => 'lha'),
        array('MIME' => 'application/x-lzh', 'EXT' => 'lzh'),
        array('MIME' => 'application/x-tar', 'EXT' => 'tar'),
        array('MIME' => 'application/x-tar', 'EXT' => 'tgz'),
        // その他
        array('MIME' => 'application/octet-stream', 'EXT' => 'tar'),
        array('MIME' => 'application/octet-stream', 'EXT' => 'tgz'),
    );
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
    static function getUrlData($url){
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

    /**
     * @param $data
     * @return null|string
     */
    static function saveTmpFile($data)
    {
        if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $data)) {
            $data = self::getUrlData($data);
        }

        $filename = tempnam(sys_get_temp_dir(), 'Tux');
        if ($fp = @fopen($filename, 'ab')) {
            $success = true;
            if (@flock($fp, LOCK_EX)) {
                if (@fwrite($fp, $data) === FALSE) {
                    $success = false;
                }
                @flock($fp, LOCK_UN);
            }else {
                $success = false;
            }
            @fclose($fp);
            if (!$success) {
                unlink($filename);
                $filename = null;
            }
            return $filename;
        }
        return null;
    }

    /**
     * @param $filename
     * @return null
     */
    static function getFileExtension($filename)
    {
        if($filename){
            $mime_type = null;
            if($finfo = finfo_open(FILEINFO_MIME_TYPE)){
                $mime_type = finfo_file($finfo, $filename);
                finfo_close($finfo);
            }
            if($mime_type){
                $ext = null;
                foreach(self::$MIME_TYPE as $val){
                    if($val['MIME'] == $mime_type){
                        $ext = $val['EXT'];
                        break;
                    }
                }
                return $ext;
            }
        }
        return null;
    }

    /**
     * @param $filename
     * @param $data
     * @return bool
     */
    static function saveImage($filename,$data){
        $tmpfile = self::saveTmpFile($data);
        if($tmpfile){
            $success = false;
            if(@getimagesize($tmpfile)){
                if(@copy($tmpfile, $filename) ){
                    $success = true;
                }
            }
            unlink($tmpfile);
            return $success;
        }
        return false;
    }
}