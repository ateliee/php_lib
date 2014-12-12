<?php
//============================================
// class_qr.php
//============================================

//$L_PROTOCOL = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) ? "https://" : "http://";
$L_PROTOCOL = "http://";
$L_SELF_DIR = preg_replace("/^".preg_quote($_SERVER["DOCUMENT_ROOT"],"/")."(.*)/","$1",preg_replace("/¥¥¥/","/",dirname(__FILE__)));
$L_URL = "";
if(isset($_SERVER["HTTP_HOST"])){
    $L_URL = $L_PROTOCOL.$_SERVER["HTTP_HOST"].$L_SELF_DIR;
}
$L_LIB_PATH = $L_URL."/../exe/qr_img0.50i/php/qr_img.php";
/**
 * Class class_qr
 */
class class_qr{
    // 透過色
    var $transparent = 0xffffffff;

    // カラー情報を生成
    function RGB($r,$g,$b){
        return (0xff << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff);
    }
    function getRGB($rgb){
        $r = ((0x00ff0000 & $rgb) >> 16);
        $g = ((0x0000ff00 & $rgb) >> 8);
        $b = ((0x000000ff & $rgb) >> 0);
        return array($r,$g,$b);
    }
    // QRコード画像を作成する
    function createQRCoodImage($dest,$souce,$size=100,$options = array()){
        GLOBAL $L_LIB_PATH;
        $pathinfo = pathinfo($dest);
        // 保存するファイル名
        $filename = $pathinfo["basename"];
        // フォーマット
        $format = $pathinfo["extension"];

        // 透過色にする色の指定
        // RGBで0から255の数値で指定
        // 0,0,0は黒255,255,255は白になります
        // サンプルは白の部分を透過色にします
        // 黒側を透過にしたい場合はすべてに0を指定します
        list($trns_red,$trns_green,$trns_blue) = $this->getRGB($this->transparent);
        // 生成される画像のサイズを指定
        // QRコードは正方形なので縦横個別設定はしません
        $img_size = $size;

        // qr_img.phpがある場所のURL
        $url = $L_LIB_PATH;
        // qr_img.phpのオプションでs=(サイズ指定)は指定しないでください
        // 指定するとサイズ圧縮処理で生成QRコードに白と黒以外の色が混入します
        // QRコードをJPEG形式で生成させます
        $options["t"] = "J";
        $img_src = $url."?d=".urlencode($souce)."&".http_build_query($options);

        // QRコードを生成
        $curlHandler = curl_init($img_src);
        $optionSet = array(
            CURLOPT_TIMEOUT             => 30,
            CURLOPT_RETURNTRANSFER      => true,
        );
        curl_setopt_array($curlHandler, $optionSet);
        $result = curl_exec($curlHandler);
        curl_close($curlHandler);
        if (!$result) {
            return null;
        }
        // 生成処理はJPEG呼び出しで固定
        //$im_src = imagecreatefromjpeg($img_src);
        $im_src = imagecreatefromstring($result);
        // 生成失敗時処理
        if(!$im_src){
            fputs("QRコード生成失敗");
            return false;
        }
        $result = false;
        $width = imagesx($im_src);
        $height = imagesy($im_src);
        // 指定サイズのQRコードを生成するための新しいイメージ・リソース
        $im_dist = imagecreate($img_size,$img_size);
        imagecolorallocate($im_dist,255,255,255);
        // 元のQRコードをコピー
        if(imagecopyresized($im_dist,$im_src,0, 0, 0, 0, $img_size,$img_size,$width,$height)){
            // 透過処理
            // GIFかPNGでのみ有効
            if($format=="gif" || $format=="png"){
                // 指定色(に最も近い色)を透過設定
                imagecolortransparent ($im_dist,imagecolorclosest ($im_dist,$trns_red,$trns_green,$trns_blue));
            }
            // 保存用関数が違うのでswitch分岐
            switch($format){
                case("gif"):        @imagegif($im_dist,$dest);       break;
                case("jpg"):
                case("jpeg"):       @imagejpeg($im_dist,$dest);      break;
                case("png"):        @imagepng($im_dist,$dest);       break;
            }
            $result = true;
        }
        //イメージ・リソースの破棄
        imagedestroy($im_src);
        imagedestroy($im_dist);
        if($result && file_exists($dest)){
            return true;
        }
        return false;
    }
}
