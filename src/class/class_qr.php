<?php
//============================================
// class_qr.php
//============================================

//$L_PROTOCOL = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) ? "https://" : "http://";
$L_PROTOCOL = "http://";
$L_SELF_DIR = preg_replace("/^" . preg_quote($_SERVER["DOCUMENT_ROOT"], "/") . "(.*)/", "$1", preg_replace("/\\\/", "/", dirname(__FILE__)));
$L_URL = $L_PROTOCOL . $_SERVER["HTTP_HOST"] . $L_SELF_DIR;
$L_LIB_PATH = $L_URL . "/../exe/qr_img0.50i/php/qr_img.php";
//+++++++++++++++++++++++++++++
// QR�R�[�h�쐬�N���X
//+++++++++++++++++++++++++++++
class class_qr
{
    // ���ߐF
    var $transparent = 0xffffffff;

    // �J���[���𐶐�
    function RGB($r, $g, $b)
    {
        return (0xff << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff);
    }

    function getRGB($rgb)
    {
        $r = ((0x00ff0000 & $rgb) >> 16);
        $g = ((0x0000ff00 & $rgb) >> 8);
        $b = ((0x000000ff & $rgb) >> 0);
        return array($r, $g, $b);
    }

    // QR�R�[�h�摜���쐬����
    function createQRCoodImage($dest, $souce, $size = 100, $options = array())
    {
        GLOBAL $L_LIB_PATH;
        $pathinfo = pathinfo($dest);
        // �ۑ�����t�@�C����
        $filename = $pathinfo["basename"];
        // �t�H�[�}�b�g
        $format = $pathinfo["extension"];

        // ���ߐF�ɂ���F�̎w��
        // RGB��0����255�̐��l�Ŏw��
        // 0,0,0�͍�255,255,255�͔��ɂȂ�܂�
        // �T���v���͔��̕����𓧉ߐF�ɂ��܂�
        // �����𓧉߂ɂ������ꍇ�͂��ׂĂ�0���w�肵�܂�
        list($trns_red, $trns_green, $trns_blue) = $this->getRGB($this->transparent);
        // ���������摜�̃T�C�Y���w��
        // QR�R�[�h�͐���`�Ȃ̂ŏc���ʐݒ�͂��܂���
        $img_size = $size;

        // qr_img.php������ꏊ��URL
        $url = $L_LIB_PATH;
        // qr_img.php�̃I�v�V������s=(�T�C�Y�w��)�͎w�肵�Ȃ��ł�������
        // �w�肷��ƃT�C�Y���k�����Ő���QR�R�[�h�ɔ��ƍ��ȊO�̐F������܂�
        // QR�R�[�h��JPEG�`���Ő��������܂�
        $options["t"] = "J";
        $img_src = $url . "?d=" . urlencode($souce) . "&" . http_build_query($options);

        // QR�R�[�h�𐶐�
        $curlHandler = curl_init($img_src);
        $optionSet = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($curlHandler, $optionSet);
        $result = curl_exec($curlHandler);
        curl_close($curlHandler);
        if (!$result || curl_errno($curlHandler)) {
            return null;
        }
        // ����������JPEG�Ăяo���ŌŒ�
        //$im_src = imagecreatefromjpeg($img_src);
        $im_src = imagecreatefromstring($result);
        // �������s������
        if (!$im_src) {
            fputs("QR�R�[�h�������s");
            return false;
        }
        $width = imagesx($im_src);
        $height = imagesy($im_src);
        // �w��T�C�Y��QR�R�[�h�𐶐����邽�߂̐V�����C���[�W�E���\�[�X
        $im_dist = imagecreate($img_size, $img_size);
        imagecolorallocate($im_dist, 255, 255, 255);
        // ����QR�R�[�h���R�s�[
        imagecopyresized($im_dist, $im_src, 0, 0, 0, 0, $img_size, $img_size, $width, $height);

        // ���ߏ���
        // GIF��PNG�ł̂ݗL��
        if ($format == "gif" || $format == "png") {
            // �w��F(�ɍł��߂��F)�𓧉ߐݒ�
            imagecolortransparent($im_dist, imagecolorclosest($im_dist, $trns_red, $trns_green, $trns_blue));
        }
        // �ۑ��p�֐����Ⴄ�̂�switch����
        switch ($format) {
            case("gif"):
                imagegif($im_dist, $dest);
                break;
            case("jpg"):
            case("jpeg"):
                imagejpeg($im_dist, $dest);
                break;
            case("png"):
                imagepng($im_dist, $dest);
                break;
        }
        //�C���[�W�E���\�[�X�̔j��
        imagedestroy($im_src);
        imagedestroy($im_dist);
        return true;
    }
}

?>