<?php
//============================================
// class_gcm.php
//============================================
// 接続先
define('C_GCM_PUSH_URL', 'https://android.googleapis.com/gcm/send');
// 最大同時送信
define('C_GCM_REGISTRATION_ID_MAX', 1000);

//+++++++++++++++++++++++++++++
// class_gcm
//+++++++++++++++++++++++++++++
class class_gcm
{
    // API Key
    var $API_KEY = "";
    // 送信情報(必要なものを記述)
    var $DATA = array(
        //'collapse_key'    => "update",                  //  オンライン復活時に表示する文字列
        //'time_to_live'    => 60 * 60 * 24 * 28,         // クライアント端末がオフラインであった場合に、いつまでメッセージを保持するか。秒単位で指定。
        //'delay_while_idle'    => false,                 // 端末がidle時はactiveになるまで送信を待つ
        //'registration_ids'    => $registration_ids,     // 送信先ID
        //'dry_run'   => false,                           //  true:実際にはメッセージを送信しない。開発時のテスト用。
        //'data'    => array('message' => $message)       // ペイロード
    );
    // 送信先デバイス
    var $APNS_REGISTRATION_IDS = array();

    // 初期化
    function init($api_key)
    {
        $this->API_KEY = $api_key;
        return true;
    }

    function setData($data)
    {
        $this->DATA = $data;
        return $this->DATA;
    }

    function setRegistrationIDs($ids)
    {
        $this->APNS_REGISTRATION_IDS = $ids;
        return $this->APNS_REGISTRATION_IDS;
    }

    // Push通知(複数送信)
    function pushMessage()
    {
        $result_list = null;
        $num = 0;
        while (count($this->APNS_REGISTRATION_IDS) > $num) {
            $registration_ids = array_slice($this->APNS_REGISTRATION_IDS, $num, C_GCM_REGISTRATION_ID_MAX);

            $data = $this->DATA;
            $data["registration_ids"] = $registration_ids;
            $post = json_encode($data);
            $header = array(
                'Content-Type: application/json',
                'Authorization: key=' . $this->API_KEY,
                'Content-Length: ' . strlen($post)
            );

            $ch = curl_init(C_GCM_PUSH_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);

            if ($result) {
                $result = json_decode($result);
                $result_list[] = $result;
            }
            $num += C_GCM_REGISTRATION_ID_MAX;
        }

        return $result_list;
    }

    // Push通知(単一送信)
    function pushMessageSingle()
    {
        $result_list = null;
        foreach ($this->APNS_REGISTRATION_IDS as $registration_id) {
            $header = array(
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization: key=' . $this->API_KEY,
            );
            $data = $this->DATA;
            $data["registration_ids"] = $registration_ids;
            $post = http_build_query($data, '&');

            $ch = curl_init($G_ANDROID_PUSH_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);

            if ($result) {
                $result = json_decode($result);
                $result_list[] = $result;
            }
        }
        return $result_list;
    }
}

?>