<?php
/**
 * ファイルクラス
 *
 * Class class_file
 */
class class_file
{
    var $dirName = "";
    var $fileName = "";
    var $fileHandle = NULL;
    var $accessCheck = false;

    /**
     * ディレクトリの設定
     *
     * @param $dir
     */
    public function setDir($dir)
    {
        $this->dirName = $dir;
        $this->fileName = '';
    }

    /**
     * ディレクトリの作成
     *
     * @param $dirname
     * @return bool
     */
    public function makeDir($dirname)
    {
        $dirname = dirname($dirname);
        $path = $this->dirName . "/" . $dirname;
        $success = false;
        // アクセス可能か調べる
        if ($this->isAccess($path)) {
            if (mkdir($path)) {
                $this->fileName = $dirname;
                $success = true;
            }
        }
        return $success;
    }

    /**
     * ディレクトリの削除
     *
     * @param string $dirname
     * @return bool
     */
    public function deleteDir($dirname = "")
    {
        $dirname = dirname($dirname);
        $path = $this->dirName . "/" . $dirname;
        $success = false;
        // アクセス可能か調べる
        if ($this->isAccess($path)) {
            deleteDir($path);
            $success = true;
        }
        return $success;
    }

    /**
     * ファイルをオープンする
     *
     * @param $filename
     * @param $mode
     * @return bool
     */
    public function open($filename, $mode)
    {
        $path = $filename;
        if ($this->dirName != "") {
            $path = $this->dirName . "/" . $filename;
        }
        $success = false;
        // アクセス可能か調べる
        if ($this->isAccess($path)) {
            if ($this->fileHandle = fopen($path, $mode)) {
                $this->fileName = $filename;
                $success = true;
            }
        }
        return $success;
    }

    /**
     * ファイルポインタを閉じる
     */
    public function close()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }

    /**
     * ファイルの中身を全て読み取る
     *
     * @param $filename
     * @return null|string
     */
    public function readAll($filename)
    {
        $data = null;
        if ($this->open($filename, 'r')) {
            while (!feof($this->fileHandle)) {
                $line = fgets($this->fileHandle);
                $data .= $line;
            }
            $this->close();
        }
        return $data;
    }

    /**
     * ファイルの中身を配列で取得
     *
     * @param $filename
     * @return array
     */
    public function readArray($filename)
    {
        $path = $filename;
        if ($this->dirName != "") {
            $path = $this->dirName . "/" . $filename;
        }
        $ret_array = array();
        // アクセス可能か調べる
        if ($this->isAccess($path)) {
            $ret_array = file($path);
        }
        return $ret_array;
    }

    /**
     * ディレクトリの中身を全て読み取る
     *
     * @param string $filename
     * @return array
     */
    public function readDirAll($filename = "")
    {
        $data = array();
        $path = $filename;
        if ($this->dirName != "") {
            $path = $this->dirName . "/" . $filename;
        }
        if ($dh = opendir($path)) {
            while ($entry = readdir($dh)) {
                if ($entry != "." && $entry != "..") {
                    $data[] = $entry;
                }
            }
            closedir($dh);
        }
        return $data;
    }

    /**
     * CSVファイル読み込み
     *
     * @param $filename
     * @return array
     */
    public function readCSV($filename)
    {
        $valuelist = array();
        $quotes = '{$quotes}';
        if ($this->open($filename, 'r')) {
            // データの取得
            while (!feof($this->fileHandle)) {
                $line = fgets($this->fileHandle);
                $line = rtrim($line);
                if ($line != "") {
                    $line = str_replace("\\\"", $quotes, $line);
                    preg_match_all('/(?:"((?:[^"]|"")*)"|([^,"]*))(?:$|,)/', $line, $matchs);
                    $value = $matchs[1];
                    foreach ($value as $key => $val) {
                        $value[$key] = str_replace($quotes, "\"", $val);
                    }
                    // 最後の行を取り除く
                    unset($value[count($value) - 1]);
                    //$value = preg_split(',',$line);
                    /*foreach($value as $key => $val){
                            $pattern = '/^"([\s\S]*)"$/';
                            if(preg_match($pattern,$val)){
                                    $value[$key] = preg_replace($pattern,"$1",$val);
                                    $value[$key] = stripcslashes($value[$key]);
                            }
                    }*/
                    $valuelist[] = $value;
//                   mb_convert_variables("UTF-8", "SJIS-win", $csv);
                }
            }
            $this->close();
        }
        return $valuelist;
    }

    /**
     * CSVファイル書き出し
     *
     * @param $filename
     * @param $valuelist
     * @return mixed
     */
    public function writeCSV($filename, $valuelist)
    {
        if ($this->open($filename, 'w')) {
            foreach ($valuelist as $value_key => $value) {
                $num = 0;
                foreach ($value as $key => $val) {
                    $v = '"' . addslashes($val) . '"';
                    if ($num > 0) {
                        $v = "," . $v;
                    }
                    fwrite($this->fileHandle, $v);
                    $num++;
                }
                fwrite($this->fileHandle, "\n");
            }
            $this->close();
        }
        return $valuelist;
    }

    /**
     * CSVファイル読み込み(連想配列)
     *
     * @param $filename
     * @return array|null
     */
    public function readTCSV($filename)
    {
        $valuelist = null;
        if ($this->open($filename, 'r')) {
            $namevalue = array();
            // カラム名の取得
            while (!feof($this->fileHandle)) {
                $line = fgets($this->fileHandle);
                $line = rtrim($line);
                $namevalue = explode(',', $line);
                foreach ($namevalue as $nkey => $nval) {
                    if (preg_match("/^\"([\s\S]*)\"$/", $nval, $tp, PREG_OFFSET_CAPTURE)) {
                        $namevalue[$nkey] = $tp[1][0];
                    }
                }
                break;
            }
            // データの取得
            while (!feof($this->fileHandle)) {
                $line = fgets($this->fileHandle);
                $value = explode(',', $line);
                $recode = array();
                $i = 0;
                foreach ($namevalue as $name) {
                    if (isset($value[$i])) {
                        if (preg_match("/^\"([\s\S]*)\"$/", $value[$i], $tp, PREG_OFFSET_CAPTURE)) {
                            $value[$i] = $tp[1][0];
                        }
                        $recode[$name] = $value[$i];
                    } else {
                        $recode[$name] = "";
                    }
                    $i++;
                }
                $valuelist[] = $recode;
//                   mb_convert_variables("UTF-8", "SJIS-win", $csv);
            }
            $this->close();
        }
        return $valuelist;
    }

    /**
     * CSVファイル書き出し(連想配列)
     *
     * @param $filename
     * @param $valuelist
     * @return mixed
     */
    public function writeTCSV($filename, $valuelist)
    {
        if ($this->open($filename, 'w')) {
            // キーの書き出し
            $keys = $valuelist[0];
            $num = 0;
            foreach ($keys as $key => $val) {
                $v = $key;
                if ($num > 0) {
                    $v = "," . $v;
                }
                fwrite($this->fileHandle, $v);
                $num++;
            }
            fwrite($this->fileHandle, "\n");
            foreach ($valuelist as $value_key => $value) {
                $num = 0;
                foreach ($value as $key => $val) {
                    $v = $val;
                    if ($num > 0) {
                        $v = "," . $v;
                    }
                    fwrite($this->fileHandle, $v);
                    $num++;
                }
                fwrite($this->fileHandle, "\n");
            }
            $this->close();
        }
        return $valuelist;
    }

    /**
     * ファイルが存在するか調べる
     *
     * @param $filename
     * @return bool
     */
    public function exists($filename)
    {
        $path = $filename;
        if ($this->dirName != "") {
            $path = $this->dirName . "/" . $filename;
        }
        $success = false;
        if ($this->isAccess($path)) {
            // ディレクトリ
            if (is_dir($path)) {
                $success = true;
            }
            // ファイル
            if (is_file($path)) {
                $success = true;
            }
        }
        return $success;
    }

    /**
     * ファイルにアクセス可能か調べる
     *
     * @param $path
     * @return bool
     */
    public function isAccess($path)
    {
        if ($this->accessCheck && $this->dirName != "") {
            $p = $path;
            /*
            // Directory Traversal対策
            while(1){
                  $count = 0;
                  $p = str_replace('../','',$p,$count);
                  if($count <= 0){
                        break;
                  }
            }
            */
            $pathinfo = pathinfo($p);
//             $p_dir = (OS_WINDOWS) ? getcwd() : $_ENV['PWD'];
            // 指定ディレクトリ配下のものか調べる
            $pattern = '/^' . preg_quote(realpath($this->dirName)) . '/';
            $subject = realpath($pathinfo['dirname'] . "/") . '/';
            if (preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE) > 0) {
                return true;
            }
            return false;
        }
        return true;
    }
}
