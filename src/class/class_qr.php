<?php
/**
 * Class class_qr
 */
class class_qr{
    // 透過色
    private $transparent;
    static private $lib_url;

    function __construct()
    {
        $this->transparent = 0xffffffff;
    }

    /**
     * @return mixed
     */
    public static function getLibUrl()
    {
        return self::$lib_url;
    }

    /**
     * @param mixed $lib_url
     */
    public static function setLibUrl($lib_url)
    {
        self::$lib_url = $lib_url;
    }

    /**
     * @param mixed $transparent
     */
    public function setTransparent($transparent)
    {
        $this->transparent = $transparent;
    }

    /**
     * @return mixed
     */
    public function getTransparent()
    {
        return $this->transparent;
    }

    /**
     * カラー情報を生成
     *
     * @param $r
     * @param $g
     * @param $b
     * @return int
     */
    private function RGB($r,$g,$b){
        return (0xff << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff);
    }

    /**
     * @param $rgb
     * @return array
     */
    private function getRGB($rgb){
        $r = ((0x00ff0000 & $rgb) >> 16);
        $g = ((0x0000ff00 & $rgb) >> 8);
        $b = ((0x000000ff & $rgb) >> 0);
        return array($r,$g,$b);
    }

    /**
     * QRコード画像を作成する
     *
     * @param $dest
     * @param $souce
     * @param int $size
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function createQRCoodImage($dest,$souce,$size=100,$options = array()){
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
        if(!self::$lib_url){
            throw new Exception(sprintf('Unset $lib_url.please set %s::setLibUrl();',get_class($this)));
        }
        $url = self::$lib_url;
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
