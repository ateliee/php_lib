<?php
//============================================
// class_apns.php
//============================================
// 接続先
define('C_APNS_SOCKET_SANDBOX', 'ssl://gateway.sandbox.push.apple.com:2195');
define('C_APNS_SOCKET', 'ssl://gateway.push.apple.com:2195');
define('C_APNS_MESSAGE_SIZE', 256);

/**
 * APNS Server Side Class
 */
class class_apns
{
    // 接続用PEMのパス
    var $APNS_PATH = "";
    // Put your private key's passphrase here:
    var $APNS_PASSWORD = '';
    // APS設定情報(必要なものを記述)
    var $APNS_BODY = array(
        //'alert' => $message,
        //"badge" => 1,
        //'sound' => 'default',
        //'content-available' => 1        // バックグラウンド起動
    );
    var $DEBUG = false;
    // 送信先デバイス
    var $APNS_DEVICE_TOKEN = array();

    /**
     * init
     * @param string $path
     * @param  string $password
     * @return bool
     */
    function init($path, $password)
    {
        $this->APNS_PATH = $path;
        $this->APNS_PASSWORD = $password;
        return true;
    }

    /**
     * set debug mode
     * @param bool $debug
     * @return bool
     */
    function setDebug($debug)
    {
        $this->DEBUG = $debug;
    }

    /**
     * set body data
     * @param array $body
     * @return bool
     */
    function setBody($body)
    {
        $this->APNS_BODY = $body;
        return $this->APNS_BODY;
    }

    /**
     * send device token
     * @param array $token
     * @return array token data
     */
    function setDeviceToken($token)
    {
        $this->APNS_DEVICE_TOKEN = $token;
        return $this->APNS_DEVICE_TOKEN;
    }

    /**
     * connect APNS
     * @param string $sslclient
     * @param string $pem_path pern path
     * @param  string $passphrase
     * @param  bool $retry connect failed retry flag
     * @return bool
     */
    function connectAPNS($sslclient, $pem_path, $passphrase, $retry = false)
    {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_path);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client($sslclient, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp) {
            // 接続エラーになったので1秒後再接続を試みます
            if ($retry) {
                sleep(1);
                return $this->connectAPNS($sslclient, $pem_path, $passphrase);
            }
        }
        return $fp;
    }

    /**
     * device token and message size
     * @param array $deviceToken
     * @return int
     */
    function getMessageSize($deviceToken)
    {
        // Create the payload body
        $body = array();
        $body['aps'] = $this->APNS_BODY;
        // Encode the payload as JSON
        $payload = json_encode($body);

        $msg = $this->getMessage($deviceToken, $payload);
        return strlen($msg);
    }

    /**
     * get message data for APNS
     * @param array $deviceToken
     * @param  string $payload
     * @return string
     */
    function getMessage($deviceToken, $payload)
    {
        return chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    }

    /**
     * push message
     * @return array send message data
     */
    function pushMessage()
    {
        $apns_socket = C_APNS_SOCKET;
        if ($this->DEBUG) {
            $apns_socket = C_APNS_SOCKET_SANDBOX;
        }
        $fp = $this->connectAPNS($apns_socket, $this->APNS_PATH, $this->APNS_PASSWORD, true);
        if (!$fp) {
            //exit("Failed to connect".PHP_EOL);
            return false;
        }
        $result_list = null;
        // Create the payload body
        $body = array();
        $body['aps'] = $this->APNS_BODY;
        // Encode the payload as JSON
        $payload = json_encode($body);
        $size = 0;
        foreach ($this->APNS_DEVICE_TOKEN as $deviceToken) {
            $msg = $this->getMessage($deviceToken, $payload);
            $msg_size = strlen($msg);
            $result = fwrite($fp, $msg, $msg_size);
            // 接続成功
            if ($result) {
                $result_list[] = $result;
            }
            $size += $msg_size;
            // 1回の通信で全パケットが5000?7000バイトを超えるとAPNSから切断されるので再接続
            if ($size >= 5120) {
                fclose($fp);
                sleep(1);
                $fp = $this->connectAPNS($apns_socket, $this->APNS_PATH, $this->APNS_PASSWORD, true);
                $size = 0;
            }
        }
        fclose($fp);

        return $result_list;
    }
}
