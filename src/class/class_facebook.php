<?php
//============================================
// class_facebook.php
//============================================
// ライブラリ読み込み

/**
 * Class class_facebook
 */
class class_facebook{
    /**
     * @var Facebook\FacebookSession
     */
    protected $facebook;
    // アプリケーションID
    protected $apiId;
    // シークレットキー
    protected $secret_key;
    // クッキー
    //protected $cookie_use;
    // エラー
    protected $error;

    static $GRAPH_API_VERSION = 'v2.3';
    static $VENDOR_DIR = 'vendor';

    protected $redirect_session;

    function __construct()
    {
        if($verndor_dir = getVendorDir('',self::$VENDOR_DIR)){
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                define('FACEBOOK_SDK_OFFICIAL',true);
                define('FACEBOOK_SDK_V4_SRC_DIR', $verndor_dir.'/facebook/fb-php-sdk-v4/src/Facebook/');
                require_once($verndor_dir.'/facebook/fb-php-sdk-v4/autoload.php');
            }else{
                define('FACEBOOK_SDK_OFFICIAL',false);
                define('FACEBOOK_SDK_V4_SRC_DIR', $verndor_dir.'/sleepwalker/facebook-php-sdk-v4/src/Facebook/');
                require_once($verndor_dir. '/sleepwalker/facebook-php-sdk-v4/autoload.php');
            }
        }else{
            throw new Exception('Not Found Vendor Dir.');
        }
    }
    // パーミッション
    /*static protected $PERMISSION = array(
        'status_update',        // ステータス更新
        'publish_stream',       // フィードへの書き込み許可
        'manage_pages',         // 管理しているファンページへのアクセス許可
        'read_friendlists',     // フレンドリストを取得
        'user_about_me',        // aboutプロパティで、プロフィールの自己紹介セクションにアクセス
        'user_birthday',        // birthdayプロパティで、生年月日にアクセス
        'user_checkins',        // チェックイン情報取得
        'email',                // 主要なメールアドレス。
        'user_location',        // locationプロパティから、居住地にアクセス
        'read_mailbox',         // ユーザのメッセージボックスへのアクセス
        'manage_pages',         // ユーザが管理するFacebookページとアプリページのアクセストークンを取得します。
        'user_activities',      // activitiesコネクションから、アクティビティ一覧にアクセス
        'user_likes',           // likesコネクションから、いいね！の一覧にアクセス
        'user_events',          // eventsコネクションから、参加するイベント一覧にアクセス
        //'offline_access',       // オフラインでのアクセス許可(アクセストークンが無期限で利用可能) ⇒ 2012削除
    );*/

    /**
     * @return mixed
     */
    public function getError(){
        return $this->error;
    }

    /**
     * @return null|string
     */
    public function getRequestUrl(){
        $url = null;
        if(isset($_SERVER['SERVER_NAME'])){
            $protocol = 'http';
            if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')){
                $protocol = 'https';
            }
            $url = $protocol.'://'.$_SERVER['SERVER_NAME'];
            if(isset($_SERVER['REQUEST_URI'])){
                $url .= $_SERVER['REQUEST_URI'];
            }
        }
        return $url;
    }

    /**
     * 初期化
     *
     * @param $appid
     * @param $seckey
     * @param bool $cookie
     * @return bool
     */
    public function init($appid,$seckey){
        $this->apiId = $appid;
        $this->secret_key = $seckey;
        //$this->cookie_use = $cookie;

        // Facebookクラス生成
        Facebook\FacebookSession::setDefaultApplication($this->apiId, $this->secret_key);

        /*
        $this->facebook = new Facebook(array(
            'appId'  => $this->apiId,
            'secret' => $this->secret_key,
            'cookie' => $this->cookie_use,
        ));*/
        return true;
    }
    // facebookクラス取得
    /*function getFacebook(){
        return $this->facebook;
    }*/

    /**
     * ログインしているか調べる
     *
     * @return Facebook\FacebookSession|null
     */
    public function getSession(){
        $helper = new Facebook\FacebookCanvasLoginHelper();
        try {
            $session = $helper->getSession();
        } catch (Facebook\FacebookRequestException $ex) {
            $this->error = $ex;
            // When Facebook returns an error
        } catch (\Exception $ex) {
            $this->error = $ex;
            // When validation fails or other local issues
        }
        if ($session) {
            return $session;
        }
        return null;
    }

    /**
     * @param $redirect_uri
     * @return Facebook\FacebookSession|null
     */
    public function getSessionFromRedirect($redirect_uri){
        if($this->redirect_session){
            return $this->redirect_session;
        }
        $helper = new Facebook\FacebookRedirectLoginHelper($redirect_uri);
        try {
            $session = $helper->getSessionFromRedirect();
            if ($session) {
                $this->redirect_session = $session;
                return $session;
            }
        } catch (Facebook\FacebookRequestException $ex) {
            $this->error = $ex;
            // When Facebook returns an error
            //throw $ex;
        } catch (\Exception $ex) {
            $this->error = $ex;
            // When validation fails or other local issues
            //throw $ex;
        }
        return null;
    }

    /**
     * @param $token
     * @return $this
     */
    public function setAccessToken($token){
        $this->facebook = new Facebook\FacebookSession($token);
        return $this;
    }

    /**
     * @param $token
     * @return \Facebook\GraphSessionInfo
     */
    public function getUserData($token){
        $session = new Facebook\FacebookSession($token);
        $user = $session->getSessionInfo();
        return $user;
    }

    /**
     * @param $method
     * @param $path
     * @param null $parameters
     * @return mixed|Facebook\GraphObject
     * @throws Exception
     */
    public function api($method, $path, $parameters = null) {
        $res = $this->apiCall( $method, $path, $parameters);
        if($res){
            $response = $res->getGraphObject();
            return $response;
        }
        return null;
    }

    /**
     * @param $method
     * @param $path
     * @param null $parameters
     * @return mixed|Facebook\GraphObject[]
     * @throws Exception
     */
    public function apiList($method, $path, $parameters = null) {
        $res = $this->apiCall( $method, $path, $parameters);
        if($res){
            $response = $res->getGraphObjectList();
            return $response;
        }
        return null;
    }

    /**
     * @throws Facebook\FacebookRequestException
     */
    /*protected function getAppAccessToken(){
        try{
            $response = (new Facebook\FacebookRequest(
                Facebook\FacebookSession::newAppSession($this->apiId, $this->secret_key),
                'GET',
                '/oauth/access_token',
                array(
                    'redirect_uri' => $this->getRequestUrl()
                )
            ))->execute()->getResponse();
        }catch (Exception $ex){
            echo $ex->getMessage();
        }

        var_dump('aaa');
        exit;
        // Graph v2.3 and greater return objects on the /oauth/access_token endpoint
        $accessToken = null;
        if (is_object($response) && isset($response->access_token)) {
            $accessToken = $response->access_token;
        } elseif (is_array($response) && isset($response['access_token'])) {
            $accessToken = $response['access_token'];
        }
        return $accessToken;
    }*/

    /**
     * @param $method
     * @param $path
     * @param null $parameters
     * @return \Facebook\FacebookResponse|null
     * @throws Facebook\FacebookRequestException
     */
    protected function apiCall($method, $path, $parameters = null) {
        try{
            $session = $this->facebook;
            if(!$session){
                $session = Facebook\FacebookSession::newAppSession($this->apiId, $this->secret_key);
            }
            $version = null;
            if(!defined('FACEBOOK_SDK_OFFICIAL') || !FACEBOOK_SDK_OFFICIAL){
                $version = self::$GRAPH_API_VERSION;
            }
            $req = (new Facebook\FacebookRequest($session, $method, $path, $parameters , $version));
            $response = $req->execute();
            return $response;
        }catch (\Facebook\FacebookAuthorizationException $ex){
            $this->error = $ex;
        }catch (\Exception $ex){
            $this->error = $ex;
        }
        return null;
    }

    /**
     * アプリ許可URLを取得
     *
     * @param $config
     * @return string
     */
    public function getLoginUrl($config=array()){
        if(isset($config['redirect_uri'])){
            $url = $config['redirect_uri'];
        }else {
            $url = $this->getRequestUrl();
        }
        $scope = array();
        if(isset($config['scope'])) {
            if(is_array($config['scope'])){
                $scope = $config['scope'];
            }else if(is_string($config['scope'])){
                $scope = explode(',',$config['scope']);
            }
        }
        $helper = new Facebook\FacebookRedirectLoginHelper($url);
        $session = $helper->getSessionFromRedirect();
        return $helper->getLoginUrl($scope);
        //return $this->facebook->getLoginUrl($config);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getAppPermissionUrl($scope=array()){
        return $this->getLoginUrl(array(
            'scope' => $scope
        ));
        /*return $this->facebook->getLoginUrl(array(
            'canvas' => 1,
            'fbconnect' => 0,
            //'req_perms' => implode(',',$this->permission)       // 間違い
            'scope' => implode(',',$perm)
        ));*/
    }
    /**
     * meを取得
     *
     * @return mixed
     * @throws Facebook\FacebookRequestException
     */
    public function getMe(){
        $req = (new Facebook\FacebookRequest(
            $this->getSession(), 'GET', '/me'
        ));
        $me = $req->execute()->getGraphObject(Facebook\GraphUser::className());
        return $me;
        //return $this->facebook->api('/me');
    }

    /**
     * @param $redirect_url
     * @return \Facebook\Entities\AccessToken|null
     */
    public function getAccessToken($redirect_url){
        try {
            $session = $this->getSessionFromRedirect($redirect_url);
            if($session){
                if(method_exists($session,'getAccessToken')){
                    return $session->getAccessToken();
                }else{
                    return $session->getToken();
                }
            }
        } catch (Exception $e) {
        }
        return null;
    }

    /**
     * @param $path
     * @param $options
     * @return mixed|Facebook\GraphObject
     * @throws Exception
     * @throws Facebook\FacebookApiException
     * @throws Facebook\FacebookRequestException
     */
    public function postPhoto($path, $options){
        //$source = '@'.realpath($source);
        try {
            $session = $this->getSession();
            /*$requestParameters = array(
                'access_token' => $session->getAccessToken(),
                //'message'      => $message,
                //'image'       => $source,
                'source'       => $source,
                //basename($source) => $source
            );
            if(count($options) > 0){
                foreach($options as $k => $v){
                    $requestParameters[$k] = $v;
                }
            }*/
            //$this->facebook->setFileUploadSupport(true);
            //$response = $this->facebook->api($path, 'POST', $requestParameters);
            $param = array(
                'source' => 'url',
                'message' => 'message',
            );
            $data = array();
            foreach($param as $key => $val) {
                if(isset($options[$key])) {
                    $data[$val] = $options[$key];
                }
            }

            $session = new Facebook\FacebookSession($session->getAccessToken());
            $req = (new Facebook\FacebookRequest($session, 'POST', $path, $data));
            $response = $req->execute()->getGraphObject();
        } catch (Facebook\FacebookApiException $e) {
            throw $e;
        }
        return $response;
    }
}