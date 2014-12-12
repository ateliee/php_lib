<?php
//============================================
// class_twitter.php
//============================================
require_once(dirname(__FILE__) . "/../library/twitteroauth/twitteroauth.php");

//+++++++++++++++++++++++++++++
// Twitterクラス
//+++++++++++++++++++++++++++++
class class_twitter
{
    // twitterクラス
    var $twitter = null;

    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    protected function constructSessionVariableName($key)
    {
        return implode('_', array('tw', $this->getTwitter()->consumer->key, $key));
    }

    // twitterクラス取得
    function getTwitter()
    {
        return $this->twitter;
    }

    // 初期化
    function init($consumerKey, $consumerSecret, $accessToken = NULL, $accessTokenSecret = NULL)
    {
        // Twitterクラス生成
        $this->twitter = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
        if ($this->twitter) {
            return true;
        }
        return false;
    }

    function get($url, $parameters = array())
    {
        return $this->twitter->get($url, $parameters);
    }

    function post($url, $parameters = array())
    {
        return $this->twitter->post($url, $parameters);
    }

    function oAuthRequest($url, $method, $parameters)
    {
        return $this->twitter->oAuthRequest($url, $method, $parameters);
    }

    // ログインURLを取得
    function getLoginURL($redirect_uri)
    {
        // callbackURLを指定してRequest tokenを取得
        $tok = $this->getTwitter()->getRequestToken($redirect_uri);

        $session_var_name = $this->constructSessionVariableName("oauth_request_token");
        $_SESSION[$session_var_name] = $tok['oauth_token'];
        $session_var_name = $this->constructSessionVariableName("oauth_request_token_secret");
        $_SESSION[$session_var_name] = $tok['oauth_token_secret'];
        // AuthorizeURLを取得
        return $this->getTwitter()->getAuthorizeURL($tok['oauth_token']);
    }

    // アクセストークンを取得
    function getUserAccessToken()
    {
        $session_access_token = $this->constructSessionVariableName("access_token");

        $token = NULL;
        if (isset($_SESSION[$session_access_token])) {
            $token = $_SESSION[$session_access_token];
            if (!$token || !isset($token["oauth_token"])) {
                unset($_SESSION[$session_access_token]);
            }
        }
        if (!$token) {
            $session_request_token = $this->constructSessionVariableName("oauth_request_token");
            $session_request_token_secret = $this->constructSessionVariableName("oauth_request_token_secret");
            if (isset($_SESSION[$session_request_token]) && isset($_SESSION[$session_request_token_secret])) {
                $to = new TwitterOAuth($this->getTwitter()->consumer->key, $this->getTwitter()->consumer->secret, $_SESSION[$session_request_token], $_SESSION[$session_request_token_secret]);

                $token = $to->getAccessToken($_REQUEST['oauth_verifier']);
                if (!$token || !isset($token["oauth_token"])) {
                    unset($_SESSION[$session_request_token]);
                    unset($_SESSION[$session_request_token_secret]);
                } else {
                    $session_var_name = $this->constructSessionVariableName("access_token");
                    $_SESSION[$session_var_name] = $token;
                }
            }
        }
        return $token;
    }
}
