<?php
//============================================
// class_facebook.php
//============================================
// ライブラリ読み込み
$version = explode('.', PHP_VERSION);
if ($version[0] >= 5) {
    require_once(dirname(__FILE__) . "/../library/facebook-sdk/facebook.php");
} else {
    require_once(dirname(__FILE__) . "/../library/facebook-sdk/facebook_php4.php");
}
//+++++++++++++++++++++++++++++
// Facebookクラス
//+++++++++++++++++++++++++++++
class class_facebook
{
    // facebookクラス
    var $facebook = null;
    // アプリケーションID
    var $apiId = "";
    // シークレットキー
    var $secret_key = "";
    // クッキー
    var $cookie_use = true;
    // パーミッション
    var $PERMISSION = array(
        'status_update', // ステータス更新
        'publish_stream', // フィードへの書き込み許可
        'manage_pages', // 管理しているファンページへのアクセス許可
        'read_friendlists', // フレンドリストを取得
        'user_about_me', // aboutプロパティで、プロフィールの自己紹介セクションにアクセス
        'user_birthday', // birthdayプロパティで、生年月日にアクセス
        'user_checkins', // チェックイン情報取得
        'email', // 主要なメールアドレス。
        'user_location', // locationプロパティから、居住地にアクセス
        'read_mailbox', // ユーザのメッセージボックスへのアクセス
        'manage_pages', // ユーザが管理するFacebookページとアプリページのアクセストークンを取得します。
        'user_activities', // activitiesコネクションから、アクティビティ一覧にアクセス
        'user_likes', // likesコネクションから、いいね！の一覧にアクセス
        'user_events', // eventsコネクションから、参加するイベント一覧にアクセス
        //'offline_access',       // オフラインでのアクセス許可(アクセストークンが無期限で利用可能) ⇒ 2012削除
    );

    // 初期化
    function init($appid, $seckey, $cookie = true)
    {
        $this->apiId = $appid;
        $this->secret_key = $seckey;
        $this->cookie_use = $cookie;

        // Facebookクラス生成
        $this->facebook = new Facebook(array(
            'appId' => $this->apiId,
            'secret' => $this->secret_key,
            'cookie' => $this->cookie_use,
        ));
        return true;
    }

    // facebookクラス取得
    function getFacebook()
    {
        return $this->facebook;
    }

    // ログインしているか調べる
    function getUser()
    {
        return $this->facebook->getUser();
    }

    // API
    public function api( /* polymorphic */)
    {
        $args = func_get_args();
        return call_user_func_array(array($this->facebook, 'api'), $args);
    }

    // アプリ許可URLを取得
    function getLoginUrl($config)
    {
        return $this->facebook->getLoginUrl($config);
    }

    function getAppPermissionUrl($perm)
    {
        return $this->facebook->getLoginUrl(array(
            'canvas' => 1,
            'fbconnect' => 0,
            //'req_perms' => implode(',',$this->permission)       // 間違い
            'scope' => implode(',', $perm)
        ));
    }

    // meを取得
    public function getMe()
    {
        return $this->facebook->api('/me');
    }

    public function postPhoto($path, $source, $options = array())
    {
        $source = '@' . realpath($source);
        try {
            $requestParameters = array(
                'access_token' => $this->facebook->getAccessToken(),
                //'message'      => $message,
                //'image'       => $source,
                'source' => $source,
                //basename($source) => $source
            );
            if (count($options) > 0) {
                foreach ($options as $k => $v) {
                    $requestParameters[$k] = $v;
                }
            }
            $this->facebook->setFileUploadSupport(true);
            $response = $this->facebook->api($path, 'POST', $requestParameters);
        } catch (FacebookApiException $e) {
            throw $e;
        }
        return $response;
    }
}
