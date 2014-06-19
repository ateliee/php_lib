<?php
// ========================================
// facebookクラス
// ========================================

class Facebook{
    var $appId = "";
    var $apiSecret = "";
    var $fileUploadSupport = false;
    var $signedRequest;
    var $state;
    var $user;
    var $accessToken = null;
    
    var $DOMAIN_MAP = array(
        'api'       => 'https://api.facebook.com/',
        'api_video' => 'https://api-video.facebook.com/',
        'api_read'  => 'https://api-read.facebook.com/',
        'graph'     => 'https://graph.facebook.com/',
        'www'       => 'https://www.facebook.com/',
    );
    
    //------------------------------------------------------------------------
    var $kSupportedKeys = array('state', 'code', 'access_token', 'user_id');
    
    function setPersistentData($key, $value) {
        if (!in_array($key, $this->kSupportedKeys)) {
            return;
        }
        $session_var_name = $this->constructSessionVariableName($key);
        $_SESSION[$session_var_name] = $value;
    }
    function getPersistentData($key, $default = false) {
        if (!in_array($key, $this->kSupportedKeys)) {
            //self::errorLog('Unsupported key passed to getPersistentData.');
            return $default;
        }
        $session_var_name = $this->constructSessionVariableName($key);
        return isset($_SESSION[$session_var_name]) ? $_SESSION[$session_var_name] : $default;
    }
    function clearPersistentData($key) {
        if (!in_array($key, $this->kSupportedKeys)) {
            //self::errorLog('Unsupported key passed to clearPersistentData.');
            return;
        }
        $session_var_name = $this->constructSessionVariableName($key);
        unset($_SESSION[$session_var_name]);
    }
    function clearAllPersistentData() {
        foreach ($this->kSupportedKeys as $key) {
            $this->clearPersistentData($key);
        }
    }
    function constructSessionVariableName($key) {
        return implode('_', array('fb',$this->appId,$key));
    }
    
    //------------------------------------------------------------------------
    function Facebook($config){
        if (!session_id()) {
            session_start();
        }
        $appId = $config["appId"];
        $apiSecret = $config["secret"];
        if (isset($config['fileUpload'])) {
                $this->setFileUploadSupport($config['fileUpload']);
        }
        $state = $this->getPersistentData('state');
        if (!empty($state)) {
          $this->state = $this->getPersistentData('state');
        }
        $this->__construct();
    }
    public function setAppId($appId) {
        $this->appId = $appId;
        return $this;
    }
    function getAppId() {
        return $this->appId;
    }
    function setApiSecret($apiSecret) {
        $this->apiSecret = $apiSecret;
        return $this;
    }
    function getApiSecret() {
        return $this->apiSecret;
    }
    function setFileUploadSupport($fileUploadSupport) {
        $this->fileUploadSupport = $fileUploadSupport;
        return $this;
    }
    function useFileUploadSupport() {
        return $this->fileUploadSupport;
    }
    function establishCSRFTokenState() {
        if ($this->state === null) {
            $this->state = md5(uniqid(mt_rand(), true));
            $this->setPersistentData('state', $this->state);
        }
    }
    // ログインURLを取得
    function getLoginUrl($params = array()){
    
        $this->establishCSRFTokenState();
        $currentUrl = $this->getCurrentUrl();

        // if 'scope' is passed as an array, convert to comma separated list
        $scopeParams = isset($params['scope']) ? $params['scope'] : null;
        if ($scopeParams && is_array($scopeParams)) {
            $params['scope'] = implode(',', $scopeParams);
        }

        return $this->getUrl(
            'www',
            'dialog/oauth',
            array_merge(array(
                'client_id' => $this->appId,
                'redirect_uri' => $currentUrl, // possibly overwritten
                'state' => $this->state),
                (array)$params));
        /*$_SESSION['facebook_state'] = md5(uniqid(rand(), TRUE));
        $authorize_url = 'https://www.facebook.com/dialog/oauth?'
            . 'client_id='. $this->appId
            //. '&redirect_uri='. rawurlencode(FACEBOOK_CONNECT)
            . '&redirect_uri='. $this->getCurrentUrl()
            . '&scope='. rawurlencode(isset($param['scope']) ? $param['scope'] : "")
            . '&state='. $_SESSION['facebook_state'];
        return $authorize_url;*/
    }
    
    function getUser() {
        if ($this->user !== null) {
            // we've already determined this and cached the value.
            return $this->user;
        }
        return $this->user = $this->getUserFromAvailableData();
    /*
        $signed_request = $this->getSignedRequest();
        if ($this->user !== null) {
            // we've already determined this and cached the value.
            return $this->user;
        }
        if($this->getAccessToken()){
            $me = $this->api('/me'); // 自分の情報を取得
            if(isset($me["id"])){
                return $me["id"]; // 自分のユーザー ID を取得
            }
        }
        $_SESSION['facebook_state'] = md5(uniqid(rand(), TRUE));
        return null;*/
    }
    function getUserFromAvailableData() {
        $signed_request = $this->getSignedRequest();
        if ($signed_request) {
            if (array_key_exists('user_id', $signed_request)) {
                $user = $signed_request['user_id'];
                $this->setPersistentData('user_id', $signed_request['user_id']);
                return $user;
            }
            // if the signed request didn't present a user id, then invalidate
            // all entries in any persistent store.
            $this->clearAllPersistentData();
            return 0;
        }
        $user = $this->getPersistentData('user_id', $default = 0);
        $persisted_access_token = $this->getPersistentData('access_token');
        // use access_token to fetch user id if we have a user access_token, or if
        // the cached access token has changed.
        $access_token = $this->getAccessToken();
        if ($access_token &&
            $access_token != $this->getApplicationAccessToken() &&
            !($user && $persisted_access_token == $access_token)) {
                $user = $this->getUserFromAccessToken();
            if ($user) {
                $this->setPersistentData('user_id', $user);
            } else {
                $this->clearAllPersistentData();
            }
        }
        return $user;
    }
    function getUserFromAccessToken() {
        $user_info = $this->api('/me');
        if(isset($user_info["id"])){
            return $user_info['id'];
        }
        return 0;
    }
    function getApplicationAccessToken() {
        return $this->appId.'|'.$this->apiSecret;
    }

    // アクセストークン取得
    function getAccessToken() {
        if ($this->accessToken !== null) {
            // we've done this already and cached it.  Just return.
            return $this->accessToken;
        }
        // first establish access token to be the application
        // access token, in case we navigate to the /oauth/access_token
        // endpoint, where SOME access token is required.
        $this->accessToken = $this->getApplicationAccessToken();
        if ($user_access_token = $this->getUserAccessToken()) {
            $this->accessToken = $user_access_token;
        }
        return $this->accessToken;
    }
    function getUserAccessToken() {
        // first, consider a signed request if it's supplied.
        // if there is a signed request, then it alone determines
        // the access token.
        $signed_request = $this->getSignedRequest();
        if ($signed_request) {
            if (array_key_exists('oauth_token', $signed_request)) {
                $access_token = $signed_request['oauth_token'];
                $this->setPersistentData('access_token', $access_token);
                return $access_token;
            }

            // signed request states there's no access token, so anything
            // stored should be cleared.
            $this->clearAllPersistentData();
            return false; // respect the signed request's data, even
            // if there's an authorization code or something else
        }

        $code = $this->getCode();
        if ($code && $code != $this->getPersistentData('code')) {
        $access_token = $this->getAccessTokenFromCode($code);
        if ($access_token) {
        $this->setPersistentData('code', $code);
        $this->setPersistentData('access_token', $access_token);
        return $access_token;
        }

        // code was bogus, so everything based on it should be invalidated.
        $this->clearAllPersistentData();
        return false;
        }

        // as a fallback, just return whatever is in the persistent
        // store, knowing nothing explicit (signed request, authorization
        // code, etc.) was present to shadow it (or we saw a code in $_REQUEST,
        // but it's the same as what's in the persistent store)
        return $this->getPersistentData('access_token');
    }
    /*function getAccessToken(){
        if (isset($_SESSION['facebook_state']) && isset($_GET['state']) && ($_GET['state'] == $_SESSION['facebook_state'])){
            $token_url = 'https://graph.facebook.com/oauth/access_token'
                . '?client_id='. $this->appId
                . '&redirect_uri='. urlencode($this->getCurrentUrl())
                . '&client_secret='. $this->apiSecret
                . '&code='. $_GET['code'];
            $response = @file_get_contents($token_url);
            $params = NULL;
            parse_str($response, $params);
            $_SESSION['facebook_access_token'] = $params['access_token'];
        }
        if(isset($_SESSION['facebook_access_token'])){
            return $_SESSION['facebook_access_token'];
        }
        return false;
    }*/
    function getCode() {
        if (isset($_REQUEST['code'])) {
            if ($this->state !== null &&
                isset($_REQUEST['state']) &&
                $this->state === $_REQUEST['state']) {

                // CSRF state has done its job, so clear it
                $this->state = null;
                $this->clearPersistentData('state');
                return $_REQUEST['code'];
            } else {
                //self::errorLog('CSRF state token does not match one provided.');
                return false;
            }
        }
        return false;
    }
    // セッション取得
    function getSession(){
        // アクセストークン取得
        if($this->getAccessToken()){
            return true;
        }
        return false;
    }
    function getSignedRequestCookieName() {
        return 'fbsr_'.$this->appId;
    }
    // SignedRequestを取得
    function getSignedRequest() {
        if (!$this->signedRequest) {
            if (isset($_REQUEST['signed_request'])) {
                $this->signedRequest = $this->parseSignedRequest($_REQUEST['signed_request']);
                //setcookie($this->getSignedRequestCookieName(), $_REQUEST['signed_request'], time() + 3600*24,"/facebook/",".tempt.jp"); // クッキーの有効期限1年間
                setcookie($this->getSignedRequestCookieName(), $_REQUEST['signed_request'], time() + 3600*24,"/"); // クッキーの有効期限1年間
            } else if (isset($_COOKIE[$this->getSignedRequestCookieName()])) {
                $this->signedRequest = $this->parseSignedRequest($_COOKIE[$this->getSignedRequestCookieName()]);
            }
        }
        return $this->signedRequest;
    }
    function parseSignedRequest($signed_request) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);
        // decode the data
        $sig = $this->base64UrlDecode($encoded_sig);
        $data = json_decode($this->base64UrlDecode($payload), true);
        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            //self::errorLog('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }
        // check sig
        $expected_sig = hash_hmac('sha256', $payload,$this->apiSecret, $raw = true);
        if ($sig !== $expected_sig) {
            //self::errorLog('Bad Signed JSON signature!');
            return null;
        }
        return $data;
    }
    function base64UrlDecode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    
    function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
          ? 'https://'
          : 'http://';
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);
        $query = '';
        if (!empty($parts['query'])) {
          // drop known fb params
          $params = explode('&', $parts['query']);
          $retained_params = array();
          foreach ($params as $param) {
            if ($this->shouldRetainParam($param)) {
              $retained_params[] = $param;
            }
          }
            
          if (!empty($retained_params)) {
            $query = '?'.implode($retained_params, '&');
          }
        }
        // use port if non default
        $port =
          isset($parts['port']) &&
          (($protocol === 'http://' && $parts['port'] !== 80) ||
           ($protocol === 'https://' && $parts['port'] !== 443))
          ? ':' . $parts['port'] : '';
        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }
    
    var $DROP_QUERY_PARAMS = array(
        'code',
        'state',
        'signed_request',
    );
    function shouldRetainParam($param) {
        foreach ($this->DROP_QUERY_PARAMS as $drop_query_param) {
          if (strpos($param, $drop_query_param.'=') === 0) {
            return false;
          }
        }
        return true;
    }
    
    function api(/* polymorphic */) {
        $args = func_get_args();
        if (is_array($args[0])) {
            return $this->_restserver($args[0]);
        } else {
            return call_user_func_array(array($this, '_graph'), $args);
        }
    }
    function _restserver($params) {
        // generic application level parameters
        $params['api_key'] = $this->appId;
        $params['format'] = 'json-strings';
        $result = json_decode($this->_oauthRequest(
            $this->getApiUrl($params['method']),
            $params
        ), true);
        // results are returned, errors are thrown
        if (is_array($result) && isset($result['error_code'])) {
            //throw new FacebookApiException($result);
            return false;
        }
        return $result;
    }
    function _oauthRequest($url, $params) {
        if (!isset($params['access_token'])) {
          $params['access_token'] = $this->getAccessToken();
        }
        // json_encode all params values that are not strings
        foreach ($params as $key => $value) {
          if (!is_string($value)) {
            $params[$key] = json_encode($value);
          }
        }
        return $this->makeRequest($url, $params);
    }
    
    
    var $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'facebook-php-3.1',
    );
    function makeRequest($url, $params, $ch=null) {
        if (!$ch) {
          $ch = curl_init();
        }
        $opts = $this->CURL_OPTS;
        if ($this->fileUploadSupport) {
            $opts[CURLOPT_POSTFIELDS] = $params;
        } else {
            $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        }
        $opts[CURLOPT_URL] = $url;
        // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
        // for 2 seconds if the server does not support this header.
        if (isset($opts[CURLOPT_HTTPHEADER])) {
            $existing_headers = $opts[CURLOPT_HTTPHEADER];
            $existing_headers[] = 'Expect:';
            $opts[CURLOPT_HTTPHEADER] = $existing_headers;
        } else {
            $opts[CURLOPT_HTTPHEADER] = array('Expect:');
        }
        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);

        if (curl_errno($ch) == 60) { // CURLE_SSL_CACERT
            self::errorLog('Invalid or no certificate authority found, '.
                         'using bundled information');
            curl_setopt($ch, CURLOPT_CAINFO,
                      dirname(__FILE__) . '/fb_ca_chain_bundle.crt');
            $result = curl_exec($ch);
        }

        if ($result === false) {
            /*$e = new FacebookApiException(array(
                'error_code' => curl_errno($ch),
                'error' => array(
                'message' => curl_error($ch),
                'type' => 'CurlException',
                ),
            ));*/
            curl_close($ch);
            //throw $e;
            return false;
        }
        curl_close($ch);
        return $result;
    }

    function getApiUrl($method) {
    $READ_ONLY_CALLS =
        array('admin.getallocation' => 1,
            'admin.getappproperties' => 1,
            'admin.getbannedusers' => 1,
            'admin.getlivestreamvialink' => 1,
            'admin.getmetrics' => 1,
            'admin.getrestrictioninfo' => 1,
            'application.getpublicinfo' => 1,
            'auth.getapppublickey' => 1,
            'auth.getsession' => 1,
            'auth.getsignedpublicsessiondata' => 1,
            'comments.get' => 1,
            'connect.getunconnectedfriendscount' => 1,
            'dashboard.getactivity' => 1,
            'dashboard.getcount' => 1,
            'dashboard.getglobalnews' => 1,
            'dashboard.getnews' => 1,
            'dashboard.multigetcount' => 1,
            'dashboard.multigetnews' => 1,
            'data.getcookies' => 1,
            'events.get' => 1,
            'events.getmembers' => 1,
            'fbml.getcustomtags' => 1,
            'feed.getappfriendstories' => 1,
            'feed.getregisteredtemplatebundlebyid' => 1,
            'feed.getregisteredtemplatebundles' => 1,
            'fql.multiquery' => 1,
            'fql.query' => 1,
            'friends.arefriends' => 1,
            'friends.get' => 1,
            'friends.getappusers' => 1,
            'friends.getlists' => 1,
            'friends.getmutualfriends' => 1,
            'gifts.get' => 1,
            'groups.get' => 1,
            'groups.getmembers' => 1,
            'intl.gettranslations' => 1,
            'links.get' => 1,
            'notes.get' => 1,
            'notifications.get' => 1,
            'pages.getinfo' => 1,
            'pages.isadmin' => 1,
            'pages.isappadded' => 1,
            'pages.isfan' => 1,
            'permissions.checkavailableapiaccess' => 1,
            'permissions.checkgrantedapiaccess' => 1,
            'photos.get' => 1,
            'photos.getalbums' => 1,
            'photos.gettags' => 1,
            'profile.getinfo' => 1,
            'profile.getinfooptions' => 1,
            'stream.get' => 1,
            'stream.getcomments' => 1,
            'stream.getfilters' => 1,
            'users.getinfo' => 1,
            'users.getloggedinuser' => 1,
            'users.getstandardinfo' => 1,
            'users.hasapppermission' => 1,
            'users.isappuser' => 1,
            'users.isverified' => 1,
            'video.getuploadlimits' => 1);
        $name = 'api';
        if (isset($READ_ONLY_CALLS[strtolower($method)])) {
            $name = 'api_read';
        } else if (strtolower($method) == 'video.upload') {
            $name = 'api_video';
        }
        return $this->getUrl($name, 'restserver.php');
    }
    function getUrl($name, $path='', $params=array()) {
        $url = $this->DOMAIN_MAP[$name];
        if ($path) {
            if ($path[0] === '/') {
                $path = substr($path, 1);
            }
            $url .= $path;
        }
        if ($params) {
            $url .= '?' . http_build_query($params, null, '&');
        }
        return $url;
    }
    function _graph($path, $method = 'GET', $params = array()) {
        if (is_array($method) && empty($params)) {
        $params = $method;
        $method = 'GET';
        }
        $params['method'] = $method; // method override as we always do a POST

        $result = json_decode($this->_oauthRequest(
            $this->getUrl('graph', $path),
            $params
        ), true);

        // results are returned, errors are thrown
        if (is_array($result) && isset($result['error'])) {
            //$this->throwAPIException($result);
            return false;
        }

        return $result;
    }
    
    // FQL発行
    function queryFQL($fql){
        $url = 'https://api.facebook.com/method/fql.query'
            .'?query=' . urlencode($fql);
            //.'&format=json';
        //$result = json_decode(@file_get_contents($url));
        $result = @file_get_contents($url);
        
        // XMLパーサーの設定
        $parser = xml_parser_create();
        
        // XMLパーサーの取得
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $result, $list, $tags);
        xml_parser_free($parser);
        
        return $list;
    }
}
?>
