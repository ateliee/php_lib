<?php
//============================================
// class_youtubeAPI.php
//============================================
//-------------------------------
// ウェブ検索リクエスト
//-------------------------------
define('C_YOUTUBE_API_VIDEO','http://gdata.youtube.com/feeds/api/videos/');

//+++++++++++++++++++++++++++++
// youtubeAPIクラス
//+++++++++++++++++++++++++++++
class class_youtubeAPI{
        // アプリケーションID
        var $APPID = '';
        
//-----------------------------
// アプリケーションIDの設定
//-----------------------------
        function setAPPID($id){
                $this->APPID = $id;
        }
//-----------------------------
// 動画の情報を取得
//-----------------------------
        function getMovieData($id){
                // APIURL設定
                $api_url = C_YOUTUBE_API_VIDEO.$id;
                // APIリクエスト
                $contents = file_get_contents($api_url);
                return $contents;
        }
}

?>