<?php
//============================================
// class_system.php
//============================================

//+++++++++++++++++++++++++++++
// システムクラス
//+++++++++++++++++++++++++++++
class class_system
{
    //var     $  = 'localhost';
    // コンストラクタ
    function class_system()
    {
    }

    // 取得関数
    function getHost()
    {
        return $_SERVER["HTTP_HOST"];
    }

    function getUserAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    function getIP()
    {
        return $_SERVER["SERVER_ADDR"];
    }

    function getPort()
    {
        return $_SERVER["SERVER_PORT"];
    }

    function getProtocol()
    {
        return (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) ? "https://" : "http://";
    }

    function getDir()
    {
        return preg_replace("/^" . preg_quote($_SERVER["DOCUMENT_ROOT"], "/") . "(.*)/", "$1", preg_replace("/\\\/", "/", dirname(__FILE__)));
    }

    function getRequestUri()
    {
        return $_SERVER["REQUEST_URI"];
    }

    function getRequestFilename()
    {
        $filename = $_SERVER["REQUEST_URI"];
        if (strpos($filename, '.')) {
            $path_parts = pathinfo($filename);
            if (defined('PATHINFO_FILENAME') && isset($path_parts["filename"])) {
                $file = $path_parts["filename"];
            } else {
                $file = substr($path_parts['basename'], 0, strpos($path_parts['basename'], '.'));
            }
            if ($file != "") {
                return $file;
            }
        }
        $filename = $_SERVER["SCRIPT_NAME"];
        $path_parts = pathinfo($filename);
        if (defined('PATHINFO_FILENAME') && isset($path_parts["filename"])) {
            $file = $path_parts["filename"];
        } else {
            $file = substr($path_parts['basename'], 0, strpos($path_parts['basename'], '.'));
        }
        if ($file != "") {
            return $file;
        }
        return NULL;
    }

    // PHPInfo
    function phpInfo()
    {
        return phpinfo();
    }

    // カレントディレクトリの取得
    function getCurrent()
    {
        return getcwd();
    }

    // カレントディレクトリの変更
    function changeCwd($dir)
    {
        $beforeCh = getcwd();
        if (chdir($dir) == false) {
            return false;
        }
        return $beforeCh;
    }
}

// echo ini_get('upload_max_filesize');
//  echo ini_get('post_max_size');
//  echo ini_get('memory_limit');

?>