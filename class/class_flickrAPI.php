<?php
//============================================
// class_flickrAPI.php
//============================================
//-------------------------------
// amazonAPI
//-------------------------------
//define('C_AMAZON_API_METHOD','GET');


//+++++++++++++++++++++++++++++
// class_flickrAPIクラス
//+++++++++++++++++++++++++++++
class class_flickrAPI{
      // アクセスキー
      var     $API_KEY = '';
      // プライベートキー
      var     $SECRET_KEY = '';
      // リクエストURL
      var     $REQUEST_URL = "http://api.flickr.com/services/rest/";
      /*var     $REQUEST_URLS = array(
                "auth" => "http://api.flickr.com/services/rest/?method=flickr.auth",    // 認証
                "blogs" => "http://api.flickr.com/services/rest/?method=flickr.blogs",    // 外部ブログ投稿
                "contacts" => "http://api.flickr.com/services/rest/?method=flickr.contacts",    // contact管理（お気に入りユーザー）
                "favorites" => "http://api.flickr.com/services/rest/?method=flickr.favorites",    // favorites管理（お気に入り写真）
                "groups" => "http://api.flickr.com/services/rest/?method=flickr.groups",    // groups（グループ）管理
                "interestingness" => "http://api.flickr.com/services/rest/?method=flickr.interestingness",    // 人気写真取得
                "people" => "http://api.flickr.com/services/rest/?method=flickr.people",    // ユーザー検索
                "photos" => "http://api.flickr.com/services/rest/?method=flickr.photos",    // 写真管理・検索
                "photos.search" => "http://api.flickr.com/services/rest/?method=flickr.photos.search",
                "photos.comments" => "http://api.flickr.com/services/rest/?method=flickr.photos.comments",    // 写真ごとのコメント管理
                "photos.geo" => "http://api.flickr.com/services/rest/?method=flickr.photos.geo",    // 写真ごとの緯度経度管理
                "photos.licenses" => "http://api.flickr.com/services/rest/?method=flickr.photos.licenses",    // 写真ごとのライセンス管理
                "photos.notes" => "http://api.flickr.com/services/rest/?method=flickr.photos.notes",    // 写真ごとのノート管理
                "photos.transform" => "http://api.flickr.com/services/rest/?method=flickr.photos.transform",    // 写真の変換（回転処理）
                "photos.upload" => "http://api.flickr.com/services/rest/?method=flickr.photos.upload",    // 写真の投稿
                "photosets" => "http://api.flickr.com/services/rest/?method=flickr.photosets",    // setの管理（写真アルバム）
                "reflection" => "http://api.flickr.com/services/rest/?method=flickr.reflection",    // メソッド一覧・情報
                "tags" => "http://api.flickr.com/services/rest/?method=flickr.tags",    // タグ管理
                "test" => "http://api.flickr.com/services/rest/?method=flickr.test",    // 動作テスト用
                "urls" => "http://api.flickr.com/services/rest/?method=flickr.urls",    // URL仕様情報
      );*/
      
      function initAPI($key,$secret=null){
            $this->API_KEY = $key;
            $this->SECRET_KEY = $secret;
      }
      //-----------------------------
      // 写真検索
      //-----------------------------
      function send($key,$param = array()){
            // オプションを取得
            $opts = $param;
            $opts["method"] = "flickr.".$key;
            $opts["api_key"] = $this->API_KEY;
            $opts["format"] = "json";
            $opts["nojsoncallback"] = "1";
            $url = $this->REQUEST_URL."?".http_build_query($opts);
            if($results = $this->file_get_contents($url)){
                $response = json_decode($results,true);
                return $response;
            }
            return null;
      }
      function searchPhoto($param = array()){
            // リクエスト
            if($response = $this->send("photos.search",$param)){
                if(isset($response["photos"])){
                        foreach($response["photos"] as $key => $val){
                                if($key == "photo"){
                                        foreach($val as $k => $v){
                                                // 詳細ページ
                                                $response["photos"][$key][$k]["url"] = sprintf("http://www.flickr.com/photos/%s/%s",$v["owner"],$v["id"]);
                                                // 写真
                                                $response["photos"][$key][$k]["photo"] = sprintf("http://farm%s.static.flickr.com/%s/%s_%s.jpg",$v["farm"],$v["server"],$v["id"],$v["secret"]);
                                                // 中心部分をトリミングした75px×75px
                                                $response["photos"][$key][$k]["photo_s"] = sprintf("http://farm%s.static.flickr.com/%s/%s_%s_s.jpg",$v["farm"],$v["server"],$v["id"],$v["secret"]);
                                                // 縦横で長い方を100pxにリサイズ
                                                $response["photos"][$key][$k]["photo_t"] = sprintf("http://farm%s.static.flickr.com/%s/%s_%s_t.jpg",$v["farm"],$v["server"],$v["id"],$v["secret"]);
                                                // 縦横で長い方を500pxにリサイズ
                                                $response["photos"][$key][$k]["photo_m"] = sprintf("http://farm%s.static.flickr.com/%s/%s_%s_m.jpg",$v["farm"],$v["server"],$v["id"],$v["secret"]);
                                                // 縦横で長い方を640pxにリサイズ
                                                $response["photos"][$key][$k]["photo_z"] = sprintf("http://farm%s.static.flickr.com/%s/%s_%s_z.jpg",$v["farm"],$v["server"],$v["id"],$v["secret"]);
                                                // 縦横で長い方を1024pxにリサイズ
                                                $response["photos"][$key][$k]["photo_b"] = sprintf("http://farm%s.static.flickr.com/%s/%s_%s_b.jpg",$v["farm"],$v["server"],$v["id"],$v["secret"]);
                                        }
                                }
                        }
                }
                return $response;
            }
            return null;
      }
      function file_get_contents( $url, $timeout = 60 ){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_HEADER, false );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
            $result = curl_exec( $ch );
            curl_close( $ch );
            return $result;
      }
}
?>