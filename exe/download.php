<?php
//============================================
// download.php
//============================================

// �_�E�����[�h�����錳�t�@�C��
$source = $_GET['filename'];

if(empty($source) == false && file_exists($source)){
        $pathinfo = pathinfo($source);
        // �t�@�C���^�C�v���擾
        switch($pathinfo['extension']){
        // �e�L�X�g�E�����EMS�I�t�B�X�֘A
        case 'txt':
                $type = 'text/plain';
                break;
        case 'csv':
                $type = 'text/csv';
                break;
        case 'tsv':
                $type = 'text/tab-separated-values';
                break;
        case 'doc':
                $type = 'application/msword';
                break;
        case 'xls':
                $type = 'application/vnd.ms-excel';
                break;
        case 'ppt':
                $type = 'application/vnd.ms-powerpoint';
                break;
        case 'pdf':
                $type = 'application/pdf';
                break;
        case 'xdw':
                $type = 'application/vnd.fujixerox.docuworks';
                break;
        case 'htm':
        case 'html':
                $type = 'text/html';
                break;
        case 'css':
                $type = 'text/css';
                break;
        case 'js':
                $type = 'text/javascript';
                break;
        case 'hdml':
                $type = 'text//x-hdml';
                break;
        // �摜�֘A
        case 'jpg':
        case 'jpeg':
                $type = 'image/jpeg';
                break;
        case 'png':
                $type = 'image/png';
                break;
        case 'gif':
                $type = 'image/gif';
                break;
        case 'bmp':
                $type = 'image/bmp';
                break;
        case 'ai':
                $type = 'application/postscript';
                break;
        // �����֘A
        case 'mp3':
                $type = 'audio/mpeg';
                break;
        case 'mp4':
                $type = 'audio/mp4';
                break;
        case 'wav':
                $type = 'audio//x-wav';
                break;
        case 'mid': case 'midi':
                $type = 'audio/midi';
                break;
        case 'mmf':
                $type = 'application/x-smaf';
                break;
        // ����֘A
        case 'mpeg':
        case 'mpeg':
                $type = 'video/mpeg';
                break;
        case 'wmv':
                $type = 'video/x-ms-wmv';
                break;
        case 'swf':
                $type = 'application/x-shockwave-flash';
                break;
        case '3g2':
                $type = 'video/3gpp2';
                break;
        // ����֘A
        case 'zip':
                $type = 'application/zip';
                break;
        case 'lha': case 'lzh':
                $type = 'application/x-lzh';
                break;
        case 'tar':
        case 'tgz':
                $type = 'application/x-tar';
                break;
        // ���̑�
        case 'tar':
        case 'tgz':
                $type = 'application/octet-stream';
                break;
        }
        if(isset($type)){
                // �ۑ����̃t�@�C����(�f�t�H���g)
                $filename = $pathinfo['filename'];
                // HTTP�w�b�_���M
                header("Content-type: {$type}");
                header("Content-Disposition: attachment; filename=\"{$filename}\"");
                // �t�@�C����ǂݍ���ŏo��
                readfile($source);
                // �v���O�����I��
                exit;
        }
}
header("HTTP/1.0 404 Not Found");
?>