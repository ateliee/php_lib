<?php
/**
 *  CodeBook.php
 *  @version   0.1.1
 *  @see       http://0-oo.net/sbox/php-tool-box/code-book
 *  @copyright 2009 dgbadmin@gmail.com
 *  @license   http://0-oo.net/pryn/MIT_license.txt (The MIT license)
 */
class CodeBook {
    private $_cipher;
    private $_mode;
 
    /**
     *  コンストラクタ
     *  デフォルトは、AES（ブロック長128bits）/CBCモード
     *  @param  string  $cipher (省略可)暗号アルゴリズム
     *  @param  string  $mode   (省略可)暗号モード
     *  @see http://jp2.php.net/manual/ja/mcrypt.ciphers.php
     *  @see http://jp2.php.net/manual/ja/mcrypt.constants.php
     */
    public function __construct($cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC) {
        $this->_cipher = $cipher;
        $this->_mode = $mode;
    }
    /**
     *  暗号化する
     *  IVを渡さない場合、ランダムなIVが生成される
     *  @param  string  $key        暗号鍵
     *  @param  string  $encryptee  暗号化するデータ
     *  @param  string  $iv         (省略可)初期化ベクトル
     *  @return array   hex化した暗号化済みデータと、hex化したIV
     */
    public function encrypt($key, $encryptee, $iv = null) {
        if (!$iv) {
            $iv = $this->_getRandIV();
        }
        $bin = mcrypt_encrypt($this->_cipher, $key, $encryptee, $this->_mode, $iv);
        return array(bin2hex($bin), bin2hex($iv));
    }
    /**
     *  復号する
     *  mcrypt_encrypt()のデフォルトのパディング文字は"\0"（Null文字）
     *  @param  string  $key        復号鍵
     *  @param  string  $encrypted  暗号化されたデータ
     *  @param  string  $iv         (省略可)hex化した初期化ベクトル（ECBでは不要）
     *  @param  string  $trimChar   (省略可)除去するパディング文字
     *  @return string  復号したデータ
     */
    public function decrypt($key, $encrypted, $iv = null, $trimChar = "\0") {
        $bin = self::hex2bin($encrypted);
        if ($iv) {
            $iv = self::hex2bin($iv);
        } else {
            $iv = $this->_getRandIV();  //Warningを出さないためのダミーのIV
        }
        $decrypted = mcrypt_decrypt($this->_cipher, $key, $bin, $this->_mode, $iv);
        if ($trimChar !== false) {
            $decrypted = rtrim($decrypted, $trimChar);
        }
        return $decrypted;
    }
    /**
     *  ブロック長に合わせてパディングする
     *  @param  string  $data       パディング対象のデータ
     *  @param  string  $padChar    パディング文字
     *  @return string  パディングしたデータ
     */
    public function pad($data, $padChar) {
        $size = $this->_getBlockSize();
        return str_pad($data, ceil(strlen($data) / $size) * $size, $padChar);
    }
    /**
     *  PKCS#5でパディングする
     *  @param  string  $data   パディング対象のデータ
     *  @return string  パディングしたデータ
     */
    public function padPkcs5($data) {
        $size = $this->_getBlockSize();
        $padLen = $size - (strlen($data) % $size);
        return $data . str_repeat(chr($padLen), $padLen);
    }
    /**
     *  PKCS#5のパディングを除去する
     *  @param  string  $data   PKCS#5でパディングされたデータ
     *  @return string  パディングしたデータ
     */
    public function trimPkcs5($data) {
        return substr($data, 0, ord(substr($data, -1, 1)) * -1);
    }
    /**
     *  hex化したデータをバイナリに変換する
     *  @param  string  $hex    hex化されたデータ
     *  @return string  バイナリになったデータ
     */
    public static function hex2bin($hex) {
        return pack('H*', $hex);
    }
 
    private function _getRandIV() {
        srand();
        return mcrypt_create_iv($this->_getBlockSize(), MCRYPT_RAND);
    }
 
    private function _getBlockSize() {
        return mcrypt_get_iv_size($this->_cipher, $this->_mode);
    }
}

