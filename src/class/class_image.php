<?php
// 画像加工タイプ
define('C_IMAGE_PRO_RESIZE', 1); // リサイズ処理(荒いがはっきり)
define('C_IMAGE_PRO_RESAMPLE', 2); // サンプリング処理(きれいだがぼける)

if (!function_exists('mime_content_type')){
    function mime_content_type($filename)
    {
        $mime_type = exec('file -Ib ' . $filename);
        return $mime_type;
    }
}

/**
 * Class class_image
 */
class class_image
{
    var $image = NULL;
    var $quality = 90;
    var $processing = C_IMAGE_PRO_RESAMPLE;
    var $encode = "SJIS";
    // フォントファイル
    var $C_TYPETRUE_DIR;
    var $C_TYPETRUE_FONT = array(
        "HGRSGU" => "hgrsgu.ttc",
        "MS Gothic" => "msgothic.ttc"
    );
    // 対応済みのファイル形式
    var $C_ENABLE_TYPE = array(
        array('MIME' => 'image/jpeg', 'EXT' => 'jpg'),
        array('MIME' => 'image/pjpeg', 'EXT' => 'jpg'),
        array('MIME' => 'image/png', 'EXT' => 'png'),
        array('MIME' => 'image/gif', 'EXT' => 'gif'),
    );

    // コンストラクタ
    public function class_image()
    {
        // GDライブラリチェック
        if(!function_exists('ImageCreateFromPNG')){
            //throw new Exception('php-gd uninstall!');
        }
        $this->C_TYPETRUE_DIR = dirname(__FILE__) . "/../exe/ttfont/";
    }

    /**
     * 処理の設定
     *
     * @param $processing
     * @return int
     */
    public function setProcessing($processing)
    {
        $this->processing = $processing;
        return $this->processing;
    }

    /**
     * @param $quality
     * @return int
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this->quality;
    }

    /**
     * 指定画像の情報を取得
     *
     * @param $filename
     * @return array
     */
    public function getImageInfo($filename)
    {
        $img_info = getimagesize($filename);
        $info = array();
        $info['width'] = $img_info[0];
        $info['height'] = $img_info[1];
        $info['type'] = $img_info[2];
        $info['attr'] = $img_info[3];
        $info['bits'] = $img_info['bits'];
        $info['channels'] = $img_info['channels'];
        $info['mime'] = $img_info['mime'];
        return $info;
    }

    /**
     * カラー情報を生成
     *
     * @param $r
     * @param $g
     * @param $b
     * @return int
     */
    public function colorRGB($r, $g, $b)
    {
        return (0xff << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff);
    }

    /**
     * @param $rgb
     * @return array
     */
    public function getRGB($rgb)
    {
        $r = ((0x00ff0000 & $rgb) >> 16);
        $g = ((0x0000ff00 & $rgb) >> 8);
        $b = ((0x000000ff & $rgb) >> 0);
        return array($r, $g, $b);
    }

    /**
     * GDライブラリが使用可能か調べる
     *
     * @return bool
     */
    public function enableGD()
    {
        if (function_exists('imagecreatefromjpeg')) {
            return true;
        }
        return false;
    }
//-----------------------------
// 画像処理関数
//-----------------------------
    /**
     * 画像をブラウザまたはファイルに出力する
     *
     * @param $image
     * @param bool $filename
     * @param int $quality
     * @return bool|null
     */
    public function outputImage($image, $filename = false, $quality = 90)
    {
        if ($image) {
            return $this->outputImageData($image, $filename, null, $quality);
        }
        return NULL;
    }

    /**
     * @param $image
     * @param bool $filename
     * @param bool $type
     * @param int $quality
     * @return bool|null
     */
    public function outputImageData($image, $filename = false, $type = false, $quality = 90)
    {
        if ($image) {
            $ext = "";
            if ($filename) {
                // 画像のファイルタイプを取得
                $info = pathinfo($filename);
                $ext = strtolower($info['extension']);
            } else if ($type) {
                $ext = strtolower($type);
            }
            if ($ext) {
                // JPG
                if ($ext == 'jpg' || $ext == 'jpeg') return imagejpeg($image, $filename, $quality);
                else if ($ext == 'gif') return imagegif($image, $filename);
                else if ($ext == 'png') return imagepng($image, $filename, min(floor((100 - $quality) / 100), 9));
            }
        }
        return NULL;
    }

    /**
     * @param bool $filename
     * @param int $quality
     * @return bool|null
     */
    public function ImageOutput($filename = false, $quality = 100)
    {
        return $this->outputImage($this->image, $filename, $quality);
    }

    /**
     * 画像読み込み
     *
     * @param $filename
     * @return mixed|null|resource
     */
    public function loadImage($filename)
    {
        $this->image = $this->createImageFromFile($filename);
        return $this->image;
    }

    /**
     * 新規画像を作成
     *
     * @param $filename
     * @param null $type
     * @param int $dx
     * @param int $dy
     * @param int $dw
     * @param int $dh
     * @return mixed|null|resource
     */
    public function createImageFromFile($filename, $type = NULL, $dx = 0, $dy = 0, $dw = 0, $dh = 0)
    {
        // 画像のファイルタイプを取得
        $ext = '';
        if (empty($type)) {
            //$info = pathinfo($filename);
            //$ext = strtolower($info['extension']);
            $info = getimagesize($filename);
            $type = $info["mime"];
        }
        if ($type != "") {
            switch ($type) {
                case 'image/jpeg':
                case 'image/pjpeg':
                    $ext = 'jpeg';
                    break;
                case 'image/png':
                    $ext = 'png';
                    break;
                case 'image/gif':
                    $ext = 'gif';
                    break;
            }
        }
        //if(empty($type)){
        //      $type = mime_content_type($filename);
        //}
        $image = NULL;
        if (($dx > 0) || ($dy > 0) || ($dw > 0) || ($dh > 0)) {
            //画像生成+切り出し
            $src = $this->createImageFromFile($filename, $type);
            if ($image = @imagecreatetruecolor($dw, $dh)) {
                // コピーします
                if (@imagecopy($image, $src, 0, 0, $dx, $dy, $dw, $dh)) {
                    // メモリ開放
                    imagedestroy($src);
                    return $image;
                }
            }
        } else {
            // 画像生成
            $func = '';
            if ($ext == 'jpg' || $ext == 'jpeg'){
                $func = 'imagecreatefromjpeg';
            }else if ($ext == 'gif'){
                $func = 'imagecreatefromgif';
            }else if ($ext == 'png'){
                $func = 'imagecreatefrompng';
            }
            if($func && function_exists($func)){
                $image = @call_user_func($func,$filename);
            }else{
                trigger_error(sprintf('Not Found Method "%s".',$func),E_USER_NOTICE);
            }
            return $image;
        }
        return null;
    }

    /**
     * 空の画像を作成
     *
     * @return null
     */
    public function getImageResource()
    {
        return $this->image;
    }

    /**
     * @param $w
     * @param $h
     * @param bool $bgcolor
     * @param bool $transparent
     * @return null|resource
     */
    public function createImageResource($w, $h, $bgcolor = false, $transparent = false)
    {
        // イメージリソースを作成
        $this->image = @imagecreatetruecolor($w, $h);
        if ($this->image) {
            if ($bgcolor !== false) {
                list($r, $g, $b) = $this->getRGB($bgcolor);
                $color = imagecolorallocate($this->image, $r, $g, $b);
                imagefill($this->image, 0, 0, $color);
            }
            if ($transparent !== false) {
                list($r, $g, $b) = $this->getRGB($transparent);
                $color = imagecolorallocate($this->image, $r, $g, $b);
                // ブレンドモードを設定する
                imagealphablending($this->image, true);
                // 完全なアルファチャネルを保存する
                //imagesavealpha($this->image, true);
                // 透過処理
                imagecolortransparent($this->image, $color);
            }
        }
        return $this->image;
    }

    /**
     *
     */
    public function destoryImageResource()
    {
        // メモリ開放
        if ($this->image) {
            imagedestroy($this->image);
        }
    }

    /**
     * 画像を回転
     *
     * @param $image
     * @param $angle
     * @param $bgd_color
     * @param int $ignore_transparent
     * @return null|resource
     */
    public function rotate($image, $angle, $bgd_color, $ignore_transparent = 0)
    {
        if ($image) {
            return imagerotate($image, $angle, $bgd_color, $ignore_transparent);
        }
        return NULL;
    }

    /**
     * @param $angle
     * @param $bgd_color
     * @param int $ignore_transparent
     * @return null|resource
     */
    public function imageRotate($angle, $bgd_color, $ignore_transparent = 0)
    {
        $this->image = $this->rotate($this->image, $angle, $bgd_color, $ignore_transparent);
        return $this->image;
    }

    /**
     * 画像の方向を正す
     *
     * @param $output
     * @param $filename
     * @return bool
     */
    public function orientationFixedImage($output, $filename)
    {
        $exif_datas = @exif_read_data($filename);
        if (isset($exif_datas['Orientation'])) {
            $orientation = $exif_datas['Orientation'];
            $this->loadImage($filename);
            if ($this->image) {
                // 未定義
                if ($orientation == 0) {
                    // 通常
                } else if ($orientation == 1) {
                    // 左右反転
                } else if ($orientation == 2) {
                    $this->imageFlop();
                    // 180°回転
                } else if ($orientation == 3) {
                    $this->imageRotate(180, 0);
                    // 上下反転
                } else if ($orientation == 4) {
                    $this->imageFlip();
                    // 反時計回りに90°回転 上下反転
                } else if ($orientation == 5) {
                    $this->imageRotate(270, 0);
                    $this->imageFlip();
                    // 時計回りに90°回転
                } else if ($orientation == 6) {
                    $this->imageRotate(270, 0);
                    // 時計回りに90°回転 上下反転
                } else if ($orientation == 7) {
                    $this->imageRotate(90, 0);
                    $this->imageFlip();
                    // 反時計回りに90°回転
                } else if ($orientation == 8) {
                    $this->imageRotate(90, 0);
                }
                // 書き出し
                $this->ImageOutput($output);
                return true;
            }
        }
        return false;
    }

    /**
     * 左右反転
     *
     * @param $image
     * @return resource
     */
    public function flop($image)
    {
        // 画像の幅を取得
        $w = imagesx($image);
        // 画像の高さを取得
        $h = imagesy($image);
        // 変換後の画像の生成（元の画像と同じサイズ）
        $destImage = @imagecreatetruecolor($w, $h);
        // 逆側から色を取得
        for ($i = ($w - 1); $i >= 0; $i--) {
            for ($j = 0; $j < $h; $j++) {
                $color_index = imagecolorat($image, $i, $j);
                $colors = imagecolorsforindex($image, $color_index);
                imagesetpixel($destImage, abs($i - $w + 1), $j, imagecolorallocate($destImage, $colors["red"], $colors["green"], $colors["blue"]));
            }
        }
        return $destImage;
    }

    /**
     * @return null|resource
     */
    public function imageFlop()
    {
        $this->image = $this->flop($this->image);
        return $this->image;
    }

    /**
     * 上下反転
     *
     * @param $image
     * @return resource
     */
    public function flip($image)
    {
        // 画像の幅を取得
        $w = imagesx($image);
        // 画像の高さを取得
        $h = imagesy($image);
        // 変換後の画像の生成（元の画像と同じサイズ）
        $destImage = @imagecreatetruecolor($w, $h);
        // 逆側から色を取得
        for ($i = 0; $i < $w; $i++) {
            for ($j = ($h - 1); $j >= 0; $j--) {
                $color_index = imagecolorat($image, $i, $j);
                $colors = imagecolorsforindex($image, $color_index);
                imagesetpixel($destImage, $i, abs($j - $h + 1), imagecolorallocate($destImage, $colors["red"], $colors["green"], $colors["blue"]));
            }
        }
        return $destImage;
    }

    /**
     * @return null|resource
     */
    public function imageFlip()
    {
        $this->image = $this->flip($this->image);
        return $this->image;
    }

    /**
     * 直線を描画
     *
     * @param $image
     * @param $x1 始点
     * @param $y1 始点
     * @param $x2 終点
     * @param $y2 終点
     * @param $color 色
     * @param int $thick 太さ
     * @return bool|null
     */
    public function writeLine($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
    {
        if ($image) {
            // 塗りつぶし
            list($r, $g, $b) = $this->getRGB($color);
            $color = imagecolorallocate($image, $r, $g, $b);
            if ($thick == 1) {
                return imageline($image, $x1, $y1, $x2, $y2, $color);
            }
            $t = $thick / 2 - 0.5;
            // 点を描画
            if ($x1 == $x2 || $y1 == $y2) {
                return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
            }
            $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
            $a = $t / sqrt(1 + pow($k, 2));
            $points = array(
                round($x1 - (1 + $k) * $a), round($y1 + (1 - $k) * $a),
                round($x1 - (1 - $k) * $a), round($y1 - (1 + $k) * $a),
                round($x2 + (1 + $k) * $a), round($y2 - (1 - $k) * $a),
                round($x2 + (1 - $k) * $a), round($y2 + (1 + $k) * $a),
            );
            imagefilledpolygon($image, $points, 4, $color);
            return imagepolygon($image, $points, 4, $color);
        }
        return NULL;
    }

    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $color
     * @param int $thick
     * @return bool|null
     */
    public function imageWriteLine($x1, $y1, $x2, $y2, $color, $thick = 1)
    {
        return $this->writeLine($this->image, $x1, $y1, $x2, $y2, $color, $thick);
    }
    // 文字を描画(回転未完成)
    //    align : L(左) C(中央) R(右) T(上) M(中央) B(下)
    //    option :
    //       size : 文字サイズ
    //       x,y : 座標
    //       width,height : サイズ
    //       padding : 余白
    //       shadow-offset : 影の位置
    //       shadow-color : 影の色
    //       bold : 太字
    /*
      // 画像作成
      if($c_image->createImageResource(200,200,0xffffffff,0xffffffff)){
              // 文字の書き出し
              //$c_image->writeImageLine(10,10,100,100,0x00ff0000,4);
              // 文字列書き出し
              $text = "111111111111\n222222\n333333\nテスト";
              $c_image->writeImageText($text,"MS Gothic",0x00000000,"LT",array("padding" => 20,"shadow-offset" => 2,"shadow-color"=>0x00000000,"bold"=>1));

              header('Content-type: image/png');
              imagepng( $c_image->getImageResource() );
              // 画像の開放
              $c_image->destoryImageResource();
      }

    */
    /**
     * @param $text
     * @param $font
     * @param $color
     * @param string $align
     * @param array $option
     */
    public function writeImageText($text, $font, $color, $align = "LT", $option = array())
    {
        if ($this->image) {
            $from_encode = mb_internal_encoding();
            $to_encode = $this->encode;
            // フォントの情報取得
            $fontfile = $this->C_TYPETRUE_DIR . $this->C_TYPETRUE_FONT[$font];
            // 文字色取得
            list($r, $g, $b) = $this->getRGB($color);
            $color = imagecolorallocate($this->image, $r, $g, $b);
            // 画像サイズ取得
            $w = ImageSX($this->image);
            $h = ImageSY($this->image);
            if (isset($option["width"])) {
                $w = $option["width"];
            }
            if (isset($option["height"])) {
                $h = $option["height"];
            }

            $size = 12;
            if (isset($option["size"])) {
                $size = $option["size"];
            }
            $angle = 0;
            // 文字列を分割
            $words = explode("\n", $text);
            $words_list = array();
            $boxsize = array();
            $boxsize["width"] = 0;
            $boxsize["height"] = 0;
            foreach ($words as $key => $t) {
                // 文字のバウンディングボックス取得
                $box = $this->calculateTextBox($t, $fontfile, $size, $angle);
                $abox = $this->calculateTextBox($t, $fontfile, $size, 0);

                $word = array();
                $word["text"] = $t;
                $word["box"] = $box;
                $word["abox"] = $abox;
                $words_list[$key] = $word;
                // サイズを格納
                $boxsize["width"] = max($boxsize["width"], $abox["width"]);
                $boxsize["height"] += $abox["height"];
            }
            $base_x = 0;
            $base_y = 0;
            if (isset($option["x"])) {
                $base_x = $option["x"];
            }
            if (isset($option["y"])) {
                $base_y = $option["y"];
            }
            $padding = 0;
            if (isset($option["padding"])) {
                $padding = $option["padding"];
            }
            $shadow = null;
            if (isset($option["shadow-offset"])) {
                $shadow = array();
                $c = 0xff000000;
                if (isset($option["shadow-color"])) {
                    $c = $option["shadow-color"];
                }
                list($r, $g, $b) = $this->getRGB($c);
                $shadow["color"] = imagecolorallocate($this->image, $r, $g, $b);
                $shadow["offset"] = $option["shadow-offset"];
            }
            $bold = 0;
            if (isset($option["bold"])) {
                $bold = 1;
            }
            $radian = deg2rad($angle);
            //=====================
            // 出力位置を設定
            //=====================
            $rx = 0;
            $ry = 0;
            foreach ($words_list as $key => $word) {
                // 文字のバウンディングボックス取得
                $box = $word["box"];
                $abox = $word["abox"];

                if ($key == 0) {
                    $x = $box["left"];
                    $y = $base_y + $box["top"];
                }
                $dx = $x;
                $dy = $y;
                // 上寄せ
                if (strpos($align, "T") !== false) {
                    $dy += $base_y + $padding;
                    // 中央寄せ
                } else if (strpos($align, "M") !== false) {
                    $v = (($h - $boxsize["height"]) / 2);
                    $_x = ceil(sin($radian) * $v) * -1;
                    $_y = ceil(cos($radian) * $v);
                    $dx += $_x;
                    $dy += $_y;
                    // 下寄せ
                } else if (strpos($align, "B") !== false) {
                    $v = ($h - $boxsize["height"] - $padding);
                    $_x = ceil(sin($radian) * $v) * -1;
                    $_y = ceil(cos($radian) * $v);
                    $dx += $_x;
                    $dy += $_y;
                }
                // 左寄せ
                if (strpos($align, "L") !== false) {
                    $dx += $base_x + $padding;
                    // 中央寄せ
                } else if (strpos($align, "C") !== false) {
                    $v = ceil(($w - $base_x) / 2) - ceil($box["width"] / 2);
                    $_x = ceil(cos($radian) * $v);
                    $_y = ceil(sin($radian) * $v) * -1;
                    $dx += $_x;
                    $dy += $_y;
                    // 右寄せ
                } else if (strpos($align, "R") !== false) {
                    $v = $w - $abox["width"] - $padding;
                    $_x = ceil(cos($radian) * $v);
                    $_y = ceil(sin($radian) * $v) * -1;
                    $dx += $_x;
                    $dy += $_y;
                    //$dx += $v;
                }
                $word["x"] = $dx + $rx;
                $word["y"] = $dy + $ry;
                $words_list[$key] = $word;

                // 影をつける
                if ($shadow) {
                    // 文字を出力
                    ImageTTFText($this->image, $size, $angle, $word["x"] + $shadow["offset"], $word["y"] + $shadow["offset"], $shadow["color"], $fontfile, $word["text"]);
                }
                // 回転角より次の文字の出力位置を取得
                $v = $abox["height"];
                $_x = ceil((cos($radian) * 0) - (sin($radian) * $v)) * -1;
                $_y = ceil((cos($radian) * $v) + (sin($radian) * 0));
                $rx += $_x;
                $ry += $_y;
            }
            // 影をつける
            if ($shadow) {
                if (function_exists('imagefilter')) {
                    // 画像にフィルタを設定
                    imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
                }
            }
            $boldx = array(1, 0, 1, 0, -1, -1, 1, 0, -1);
            $boldy = array(0, -1, -1, 0, 0, -1, 1, 1, 1);
            foreach ($words_list as $key => $word) {
                if ($bold) {
                    for ($n = 0; $n <= 8; $n++) {
                        // 文字を出力
                        ImageTTFText($this->image, $size, $angle, $word["x"] + $boldx[$n], $word["y"] + $boldy[$n], $color, $fontfile, $word["text"]);
                    }
                } else {
                    // 文字を出力
                    ImageTTFText($this->image, $size, $angle, $word["x"], $word["y"], $color, $fontfile, $word["text"]);
                }
            }
        }
    }

    /**
     * @param $text
     * @param $fontFile
     * @param $fontSize
     * @param $fontAngle
     * @return array
     */
    public function calculateTextBox($text, $fontFile, $fontSize, $fontAngle)
    {
        $rect = imagettfbbox($fontSize, $fontAngle, $fontFile, $text);
        $minX = min(array($rect[0], $rect[2], $rect[4], $rect[6]));
        $maxX = max(array($rect[0], $rect[2], $rect[4], $rect[6]));
        $minY = min(array($rect[1], $rect[3], $rect[5], $rect[7]));
        $maxY = max(array($rect[1], $rect[3], $rect[5], $rect[7]));

        return array(
            "left" => abs($minX) - 1,
            "top" => abs($minY) - 1,
            "width" => $maxX - $minX,
            "height" => $maxY - $minY,
            "box" => $rect
        );
    }

    /**
     * 空の画像を作成する
     *
     * @param $filename
     * @param $w
     * @param $h
     * @param bool $transparent
     * @return null|resource
     */
    public function createImage($filename, $w, $h, $transparent = false)
    {
        // イメージリソースを作成
        $img = @imagecreatetruecolor($w, $h);
        if ($transparent) {
            // 塗りつぶし
            list($r, $g, $b) = $this->getRGB($transparent);
            $color = imagecolorallocate($img, $r, $g, $b);
            // 背景色の設定
            imagefilledrectangle($img, 0, 0, $w, $h, $color);
        }
        // 画像の保存
        $new = $this->outputImage($img, $filename, $this->quality);
        // メモリ開放
        imagedestroy($img);
        if ($new) {
            return $img;
        }
        return NULL;
    }

    /**
     * 画像フォーマット変換
     *
     * @param $dest
     * @param $src
     * @return bool
     */
    public function formatImage($dest, $src)
    {
        $img = $this->createImageFromFile($src);
        if ($img && $this->outputImage($img, $dest, 100)) {
            // メモリ開放
            imagedestroy($img);
            return true;
        }
        // メモリ開放
        imagedestroy($img);
        return false;
    }

    /**
     * 画像の大きさを変換する
     *
     * @param $dest
     * @param $src
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $dst_w
     * @param $dst_h
     * @param $src_w
     * @param $src_h
     * @return bool|null
     */
    public function processingImage($dest, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        switch ($this->processing) {
            // リサイズ処理(荒いがはっきり)
            case C_IMAGE_PRO_RESIZE:
                return ImageCopyResized($dest, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                break;
            // サンプリング処理(きれいだがぼける)
            case C_IMAGE_PRO_RESAMPLE:
                return ImageCopyResampled($dest, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                break;
            default:
                if ($dst_w <= 120 && $dst_h <= 120) {
                    return ImageCopyResampled($dest, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                } else {
                    return ImageCopyResized($dest, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                }
                break;
        }
        return NULL;
    }

    /**
     * 一部分の画像を作成
     *
     * @param $dest
     * @param $src
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     * @return bool
     */
    public function clipImage($dest, $src, $x = 0, $y = 0, $w = 0, $h = 0)
    {
        // 画像のインスタンスを作成します
        $src_img = $this->createImageFromFile($src);
        $dest_img = @imagecreatetruecolor($w, $h);
        // コピーします
        if (imagecopy($dest_img, $src_img, 0, 0, $x, $y, $w, $h)) {
            $img = $this->outputImage($dest_img, $dest, 100);
            // メモリ開放
            imagedestroy($src_img);
            imagedestroy($dest_img);
            return true;
        }
        return false;
    }

    /**
     * 画像の大きさを変換する
     *
     * @param $resource
     * @param $w
     * @param $h
     * @param bool $fit
     * @param int $dx
     * @param int $dy
     * @param int $dw
     * @param int $dh
     * @return resource
     */
    public function resizeImageFromResource($resource, $w, $h, $fit = false, $dx = 0, $dy = 0, $dw = 0, $dh = 0)
    {
        // 画像のサイズを取得。
        $sw = imagesx($resource);
        $sh = imagesy($resource);
        // 等倍変換
        $dw = $w;
        $dh = $h;
        $w = $sw;
        $h = $sh;
        // 横
        if ($w > $dw) {
            $new_rate = $dw / $sw;
            $w = $dw;
            $h = (int)($sh * $new_rate);
        }
        // 縦
        if ($h > $dh) {
            $new_rate = $dh / $sh;
            $h = $dh;
            $w = (int)($sw * $new_rate);
        }
        if ($fit) {
            /*
            // 横
            if($w < $dw){
                    $new_rate = $dw / $sw;
                    $w = $dw;
                    $h = (int)($sh * $new_rate);
            }
            // 縦
            if($h > $dh){
                    $new_rate = $dh / $sh;
                    $h = $dh;
                    $w = (int)($sw * $new_rate);
            }*/
        }
        // 空の画像を作成する。
        $dest_img = ImageCreateTrueColor($w, $h);
        // アルファチャンネルの設定
        imagealphablending($dest_img, false);
        imageSaveAlpha($dest_img, true);
        $fillcolor = imagecolorallocatealpha($dest_img, 0, 0, 0, 127);
        imagefill($dest_img, 0, 0, $fillcolor);
        // 画像をリサイズ
        $this->processingImage($dest_img, $resource, 0, 0, 0, 0, $w, $h, $sw, $sh);

        return $dest_img;
    }

    /**
     * @param $dest
     * @param $src
     * @param $w
     * @param $h
     * @param null $src_type
     * @param bool $fit
     * @param int $dx
     * @param int $dy
     * @param int $dw
     * @param int $dh
     * @return bool
     */
    public function resizeImage($dest, $src, $w, $h, $src_type = NULL, $fit = false, $dx = 0, $dy = 0, $dw = 0, $dh = 0)
    {
        // 画像のサイズを取得。
        $sw = 0;
        $sh = 0;
        if ($src != "" && file_exists($src)) {
            list($sw, $sh) = getimagesize($src);
            if ($sw <= $w && $sh <= $h) {
                return copy($src, $dest);
            }
        }
        /*// 画像のファイルタイプを取得
        $info = pathinfo($src);
        $ext = strtolower($info['extension']);
        // GIFの場合、アニメーションGIF用に縮小
        if($ext == "gif"){
                $command = "convert -size -resize ".$dw."x".$dh." ".$src." ".$dest;
                system($command,$return_var);
                if($return_var == 0){
                        return true;
                }
        }*/
        // 画像の読み込み
        $src_img = $this->createImageFromFile($src, $src_type, $dx, $dy, $dw, $dh);
        if ($src_img) {
            // 画像データの取得
            $dest_img = $this->resizeImageFromResource($src_img, $w, $h, $fit, $dx, $dy, $dw, $dh);

            $success = false;
            if ($dest_img) {
                $success = $this->outputImage($dest_img, $dest, 100);
                // メモリ開放
                imagedestroy($dest_img);
            }
            imagedestroy($src_img);

            if ($success) return true;
        }
        return false;
    }

    /**
     * トリミング画像の生成
     *
     * @param $resource
     * @param $w
     * @param $h
     * @param bool $fit
     * @param int $dx
     * @param int $dy
     * @param int $dw
     * @param int $dh
     * @return resource
     */
    public function trimmingImageFromResource($resource, $w, $h, $fit = false, $dx = 0, $dy = 0, $dw = 0, $dh = 0)
    {
        // 画像のサイズを取得。
        $sw = imagesx($resource);
        $sh = imagesy($resource);
        if ($fit == false) {
            if ($sw < $w) $w = $sw;
            if ($sh < $h) $h = $sh;
        }
        // 空の画像を作成する。
        $dest_img = ImageCreateTrueColor($w, $h);
        // アルファチャンネルの設定
        imagealphablending($dest_img, false);
        imageSaveAlpha($dest_img, true);
        $fillcolor = imagecolorallocatealpha($dest_img, 0, 0, 0, 127);
        imagefill($dest_img, 0, 0, $fillcolor);

        // 縦横の比率
        $rate = $w / $h;
        $srate = $sw / $sh;
        // 横長の画像の場合
        if ($rate >= $srate) {
            $new_rate = $w / $sw;
            $dw = $w;
            $dh = (int)($sh * $new_rate);
            // 縦長の画像の場合
        } else {
            $new_rate = $h / $sh;
            $dw = (int)($sw * $new_rate);
            $dh = $h;
        }
        $x = 0;
        $y = 0;
        $new_rate_re = 1 / $new_rate;
        // はみ出た部分を切り取る
        if ($sw > $dw) {
            $cut = ($dw - $w) / $new_rate;
            $x = (int)($cut / 2);
        }
        if ($sh > $dh) {
            $cut = ($dh - $h) / $new_rate;
            $y = (int)($cut / 2);
        }
        // 画像をリサイズ
        $this->processingImage($dest_img, $resource, 0, 0, $x, $y, $dw, $dh, $sw, $sh);

        return $dest_img;
    }

    /**
     * @param $dest
     * @param $src
     * @param $w
     * @param $h
     * @param null $src_type
     * @param bool $fit
     * @param int $dx
     * @param int $dy
     * @param int $dw
     * @param int $dh
     * @return bool
     */
    public function trimmingImage($dest, $src, $w, $h, $src_type = NULL, $fit = false, $dx = 0, $dy = 0, $dw = 0, $dh = 0)
    {
        // 画像のサイズを取得。
        $sw = 0;
        $sh = 0;
        if ($src != "" && file_exists($src)) {
            list($sw, $sh) = getimagesize($src);
            if ($sw <= $w && $sh <= $h) {
                return copy($src, $dest);
            }
        }
        // 画像の読み込み
        $src_img = $this->createImageFromFile($src, $src_type, $dx, $dy, $dw, $dh);
        if ($src_img) {
            // 画像データの取得
            $dest_img = $this->trimmingImageFromResource($src_img, $w, $h, $fit, $dx, $dy, $dw, $dh);

            $success = false;
            if ($dest_img) {
                // 画像ファイルを作成
                $success = $this->outputImage($dest_img, $dest, 100);
                // メモリ開放
                imagedestroy($dest_img);
            }
            imagedestroy($src_img);

            if ($success) return true;
        }
        return false;
    }

    /**
     * 画像を作成する
     *
     * @param $dest
     * @param $src
     * @param $type_string {$type}{$width}_{$height}で指定(S:サムネイル,F:フィット,T:トリミング)
     * @param null $src_type
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     * @return bool
     */
    public function createImageformFileEX($dest, $src, $type_string, $src_type = NULL, $x = 0, $y = 0, $w = 0, $h = 0)
    {
        return $this->createImageFromFileEX($dest, $src, $type_string, $src_type, $x, $y, $w, $h);
    }

    /**
     * @param $dest
     * @param $src
     * @param $type_string
     * @param null $src_type
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     * @return bool
     */
    public function createImageFromFileEX($dest, $src, $type_string, $src_type = NULL, $x = 0, $y = 0, $w = 0, $h = 0)
    {
        // 作成タイプを取得
        $type_string = strtoupper($type_string);
        if (preg_match("/^([A-Z])([0-9]+)_([0-9]+)$/", $type_string, $t, PREG_OFFSET_CAPTURE)) {
            $type = $t[1][0];
            $width = $t[2][0];
            $height = $t[3][0];
        }
        if (preg_match("/^([A-Z])([0-9]+)$/", $type_string, $t, PREG_OFFSET_CAPTURE)) {
            $type = $t[1][0];
            $width = $t[2][0];
            $height = $t[2][0];
        }
        $success = false;
        switch ($type) {
            // フィット(縦横の比率を保持)
            case 'F':
                $success = $this->resizeImage($dest, $src, $width, $height, $src_type, false, $x, $y, $w, $h);
                break;
            // トリミング(縦横の比率を保持しない)
            case 'T':
                $success = $this->trimmingImage($dest, $src, $width, $height, $src_type, false, $x, $y, $w, $h);
                break;
            // 縮小(縦横の比率を保持しない)
            case 'S':
                $success = $this->resizeImage($dest, $src, $width, $height, $src_type, true, $x, $y, $w, $h);
                break;
        }
        return $success;
    }

    /**
     * @param $filename
     * @param $binary
     * @param $type_string
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     * @return null|string
     */
    public function createImageFromBinaryEX($filename, $binary, $type_string, $x = 0, $y = 0, $w = 0, $h = 0)
    {
        // 作成タイプを取得
        $type_string = strtoupper($type_string);
        if (preg_match("/^([A-Z])([0-9]+)_([0-9]+)$/", $type_string, $t, PREG_OFFSET_CAPTURE)) {
            $type = $t[1][0];
            $width = $t[2][0];
            $height = $t[3][0];
        }
        if (preg_match("/^([A-Z])([0-9]+)$/", $type_string, $t, PREG_OFFSET_CAPTURE)) {
            $type = $t[1][0];
            $width = $t[2][0];
            $height = $t[2][0];
        }
        $success = false;
        $data = null;
        // 画像リソースの取得
        $src_image = @imagecreatefromstring($binary);
        if ($src_image) {
            $resource = NULL;
            switch ($type) {
                // フィット(縦横の比率を保持)
                case 'F':
                    $resource = $this->resizeImageFromResource($src_image, $width, $height, false, $x, $y, $w, $h);
                    break;
                // トリミング(縦横の比率を保持しない)
                case 'T':
                    $resource = $this->trimmingImageFromResource($src_image, $width, $height, false, $x, $y, $w, $h);
                    break;
                // 縮小(縦横の比率を保持しない)
                case 'S':
                    $resource = $this->resizeImageFromResource($src_image, $width, $height, true, $x, $y, $w, $h);
                    break;
            }
            // メモリ開放
            imagedestroy($src_image);
            // 生成された画像リソースのバイナリデータの取得
            if ($resource) {
                ob_start();
                $this->outputImageData($resource, null, pathinfo($filename, PATHINFO_EXTENSION));
                $data = ob_get_contents();
                ob_end_clean();

                // メモリ開放
                imagedestroy($resource);
                if ($data) {
                    $success = true;
                }
            }
        }
        return $data;
    }

    /**
     * 画像の合成
     *
     * @param $dest
     * @param $src
     * @param $mask
     * @return bool|null
     */
    public function maskImage($dest, $src, $mask)
    {
        // 元画像
        $image = $this->createImageFromFile($src);
        $image_w = imagesx($image);
        $image_h = imagesy($image);
        // マスク画像
        $mask_image = $this->createImageFromFile($mask);
        $mask_image_w = imagesx($mask_image);
        $mask_image_h = imagesy($mask_image);
        // キャンバス
        $canvas_width = max($image_w, $mask_image_w);
        $canvas_height = max($image_h, $mask_image_h);
        $canvas = imagecreatetruecolor($canvas_width, $canvas_height);
        // ブレンドモード無効
        imagealphablending($canvas, FALSE);
        // アルファチャンネルを保存
        imagesavealpha($canvas, TRUE);
        // 透明色で塗りつぶし
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        // 中心から切り抜くための調整
        $image_left = round(($image_w - $canvas_width) / 2);
        $image_top = round(($image_h - $canvas_height) / 2);
        $mask_left = round(($mask_image_w - $canvas_width) / 2);
        $mask_top = round(($mask_image_h - $canvas_height) / 2);

        for ($y = 0; $y < $canvas_height; $y++) {
            for ($x = 0; $x < $canvas_width; $x++) {
                // 不透明
                $alpha = 0;
                // マスク画像のアルファ値を取得
                $mask_x = ($x + $mask_left);
                $mask_y = ($y + $mask_top);
                if (($mask_x >= 0) && ($mask_x < $mask_image_w) && ($mask_y >= 0) && ($mask_y < $mask_image_h)) {
                    $rgb = imagecolorat($mask_image, $mask_x, $mask_y);
                    $index = imagecolorsforindex($mask_image, $rgb);
                    $alpha = $index['alpha'];
                }
                // 元画像の色を取得
                $image_x = ($x + $image_left);
                $image_y = ($y + $image_top);
                if (($image_x >= 0) && ($image_x < $image_w) && ($image_y >= 0) && ($image_y < $image_h)) {
                    $current = imagecolorat($image, $image_x, $image_y);
                    $index = imagecolorsforindex($image, $current);
                    // 色を再生成
                    $color = imagecolorallocatealpha($image, $index['red'], $index['green'], $index['blue'], $alpha);
                } else {
                    $alpha = 127;
                    $color = imagecolorallocatealpha($image, 255, 255, 255, $alpha);
                }
                // 塗りつぶし
                imagesetpixel($canvas, $x, $y, $color);
            }
        }
        $success = $this->outputImage($canvas, $dest, 100);
        // メモリ開放
        imagedestroy($image);
        imagedestroy($mask_image);
        imagedestroy($canvas);

        return $success;
    }
}
