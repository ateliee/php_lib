<?php
//============================================
// download.php
//============================================

// ダウンロードさせる元ファイル
$source = $_GET['filename'];

if(empty($source) == false && file_exists($source)){
        $pathinfo = pathinfo($source);
        // ファイルタイプを取得
        switch($pathinfo['extension']){
        // テキスト・文書・MSオフィス関連
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
        // 画像関連
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
        // 音声関連
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
        // 動画関連
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
        // 動画関連
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
        // その他
        case 'tar':
        case 'tgz':
                $type = 'application/octet-stream';
                break;
        }
        if(isset($type)){
                // 保存時のファイル名(デフォルト)
                $filename = $pathinfo['filename'];
                // HTTPヘッダ送信
                header("Content-type: {$type}");
                header("Content-Disposition: attachment; filename=\"{$filename}\"");
                // ファイルを読み込んで出力
                readfile($source);
                // プログラム終了
                exit;
        }
}
header("HTTP/1.0 404 Not Found");
?>