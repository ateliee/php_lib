<?php
//============================================
// class_ftp.php
//============================================

//+++++++++++++++++++++++++++++
// FTP接続クラス
//+++++++++++++++++++++++++++++
class class_ftp
{
    // FTP接続設定
    var $connect_id = null; // FTPストリーム
    var $ftp_server = "192.168.0.1"; // FTPサーバーのIP
    var $user_name = ""; // ログインID
    var $user_pass = ""; // パスワード
    var $port = 21; // ポート
    var $timeout = 90; // タイムアウト

    //====================================
    // 全般
    //====================================
    // FTP接続
    function connect()
    {
        // FTPサーバーへ接続
        $this->connect_id = ftp_connect($this->ftp_server);
        if ($this->connect_id) {
            // FTPサーバーへログイン
            $login_result = ftp_login($this->connect_id, $this->user_name, $this->user_pass);
            if ($login_result) {
                return true;
            }
        }
        return false;
    }

    // FTP接続(SSL接続)
    function ssl_connect()
    {
        // FTPサーバーへ接続
        $this->connect_id = ftp_ssl_connect($this->ftp_server);
        if ($this->connect_id) {
            // FTPサーバーへログイン
            $login_result = ftp_login($this->connect_id, $this->user_name, $this->user_pass);
            if ($login_result) {
                return true;
            }
        }
        return false;
    }

    // FTP切断
    function close()
    {
        if ($this->connect_id) {
            $result = ftp_close($this->connect_id);
            if ($result) {
                $this->connect_id = null;
                return true;
            }
        }
        return false;
    }

    // パッシブモードを変更
    function pasv($pasv)
    {
        return ftp_pasv($this->connect_id, $pasv);
    }

    //====================================
    // 取得・設定
    //====================================
    // 作業中のディレクトリを調べる
    function pwd()
    {
        return ftp_pwd($this->connect_id);
    }

    // 作業中のディレクトリを変更
    function chdir($dir)
    {
        return ftp_chdir($this->connect_id, $dir);
    }

    // ファイル一覧を取得する
    function nlist($directory)
    {
        return ftp_nlist($this->connect_id, $directory);
    }

    function rawlist($directory)
    {
        return ftp_rawlist($this->connect_id, $directory);
    }

    //====================================
    // アップロード
    //====================================
    // ファイルをアップロードする
    //       mode      :      FTP_ASCII(テキストファイルをアップロードする場合) FTP_BINARY(画像ファイルなどバイナリファイルをアップロードする場合)
    function put($remote_file, $local_file, $mode = FTP_ASCII)
    {
        $result = ftp_put($this->connect_id, $remote_file, $local_file, $mode);
        return $result;
    }
    //====================================
    // ダウンロード
    //====================================
    // ファイルをダウンロードする
    //       mode      :      FTP_ASCII(テキストファイルをアップロードする場合) FTP_BINARY(画像ファイルなどバイナリファイルをアップロードする場合)
    function get($local_file, $remote_file, $mode = FTP_ASCII)
    {
        $result = ftp_get($this->connect_id, $local_file, $remote_file, $mode);
        return $result;
    }
    //====================================
    // 作成・削除・変更
    //====================================
    // ディレクトリを作成する
    function mkdir($directory)
    {
        $result = ftp_mkdir($this->connect_id, $directory);
        return $result;
    }

    // ディレクトリを削除する
    function rmdir($directory)
    {
        $result = ftp_rmdir($this->connect_id, $directory);
        return $result;
    }

    // ファイルを削除する
    function delete($filename)
    {
        $result = ftp_delete($this->connect_id, $filename);
        return $result;
    }

    // ファイル名を変更する
    function rename($oldname, $newname)
    {
        $result = ftp_rename($this->connect_id, $oldname, $newname);
        return $result;
    }
}

?>