<?php
//============================================
// func_agent.php
//============================================

//+++++++++++++++++++++++++++++
// キャリア判別
//+++++++++++++++++++++++++++++
function get_agent($agent = NULL)
{
    $agent = ($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
}

function is_mobile_docomo_agent($agent = NULL)
{
    return is_user_agent(array("/^DoCoMo/i"), get_agent($agent));
}

function is_mobile_kddi_agent($agent = NULL)
{
    return is_user_agent(array("/^KDDI\-/i", "/UP\.Browser/i"), get_agent($agent));
}

function is_mobile_softbank_agent($agent = NULL)
{
    return is_user_agent(array("/^(J\-PHONE|Vodafone|MOT\-[CV]|SoftBank)/i"), get_agent($agent));
}

function is_mobile_willcom_agent($agent = NULL)
{
    return is_user_agent(array("/^PDXGW/i", "/(DDIPOCKET|WILLCOM)/i"), get_agent($agent));
}

function is_iphone_agent($agent = NULL)
{
    return is_user_agent(array("/iPhone/i"), get_agent($agent));
}

function is_ipad_agent($agent = NULL)
{
    return is_user_agent(array("/iPad/i"), get_agent($agent));
}

function is_ipod_agent($agent = NULL)
{
    return is_user_agent(array("/iPod/i"), get_agent($agent));
}

function is_android_agent($agent = NULL)
{
    return is_user_agent(array("/Android/i"), get_agent($agent));
}

function is_android_mobile_agent($agent = NULL)
{
    if (!is_android_agent($agent)) {
        return false;
    }
    return is_user_agent(array("/Mobile/i"), get_agent($agent));
}

function is_android_tablet_agent($agent = NULL)
{
    if (!is_android_agent($agent)) {
        return false;
    }
    return (is_android_mobile_agent($agent) ? false : true);
}

function is_tablet_agent($agent = NULL)
{
    return (is_ipad_agent($agent) || is_android_tablet_agent($agent));
}

function is_user_agent($preg_list, $agent = "")
{
    if ($agent == "") {
        $agent = $_SERVER['HTTP_USER_AGENT'];
    }
    foreach ($preg_list as $preg) {
        if (preg_match($preg, $agent)) {
            return true;
        }
    }
    return false;
}

//+++++++++++++++++++++++++++++
// キャリア判別
//+++++++++++++++++++++++++++++
function is_mobile_agent($agent = NULL)
{
    if (is_mobile_docomo_agent($agent)) return true;
    if (is_mobile_kddi_agent($agent)) return true;
    if (is_mobile_softbank_agent($agent)) return true;
    if (is_mobile_willcom_agent($agent)) return true;
    return false;
}

function is_sphone_agent($agent = NULL)
{
    if (is_iphone_agent($agent)) return true;
    //if(is_ipad_agent($agent))      return true;
    if (is_ipod_agent($agent)) return true;
    if (is_android_mobile_agent($agent)) return true;
    return false;
}

function is_pc_agent($agent = NULL)
{
    return is_mobile_agent($agent) ? false : (is_sphone_agent() ? false : true);
}

function get_agent_carrier($agent = NULL)
{
    if (is_mobile_docomo_agent($agent)) return SYSTEM_CARRIER_DOCOMO;
    if (is_mobile_kddi_agent($agent)) return SYSTEM_CARRIER_KDDI;
    if (is_mobile_softbank_agent($agent)) return SYSTEM_CARRIER_SOFTBANK;
    if (is_mobile_willcom_agent($agent)) return SYSTEM_CARRIER_WILLCOM;
    return SYSTEM_CARRIER_PC;
}

function get_mobile_agent($agent = NULL)
{
    $carrier = get_agent_carrier($agent);
    if ($carrier != SYSTEM_CARRIER_PC) {
        return $carrier;
    }
    return "";
}

//+++++++++++++++++++++++++++++
// IPアドレスによるキャリア判別
//+++++++++++++++++++++++++++++
function in_CIDR($ip, $cidr)
{
    // IPアドレス帯を取得
    list($network, $mask_bit_len) = explode('/', $cidr);
    $host = 32 - $mask_bit_len;
    $net = ip2long($network) >> $host << $host; // 11000000101010000000000000000000
    $ip_net = ip2long($ip) >> $host << $host; // 11000000101010000000000000000000
    return $net === $ip_net;
}

function get_ip_carrier()
{
    // IPのキャリアを決定する
    $ip_carrier = "";
    // IPアドレスからキャリアを判断する
    foreach (class_carrer::getCidrAll() as $carrier => $carrier_cidr) {
        foreach ($carrier_cidr as $cidr) {
            if (in_CIDR($_SERVER["REMOTE_ADDR"], $cidr)) {
                $ip_carrier = $carrier;
                break 2;
            }
        }
    }
    // 携帯のキャリアIPアドレスでない場合はPCからのアクセスとみなす
    if ($ip_carrier == "") {
        $ip_carrier = SYSTEM_CARRIER_PC;
    }
    return $ip_carrier;
}

function is_mobile_ip()
{
    // IPからのキャリア取得
    $ip_carrier = get_ip_carrier();
    if ($ip_carrier == SYSTEM_CARRIER_DOCOMO) return true;
    if ($ip_carrier == SYSTEM_CARRIER_KDDI) return true;
    if ($ip_carrier == SYSTEM_CARRIER_SOFTBANK) return true;
    if ($ip_carrier == SYSTEM_CARRIER_WILLCOM) return true;
    return false;
}

function is_pc_ip()
{
    return is_mobile_ip() ? false : true;
}

//+++++++++++++++++++++++++++++
// キャリアの総合判断
//+++++++++++++++++++++++++++++
function get_carrier()
{
    $ip_carrier = get_ip_carrier();
    $agent_carrier = get_mobile_agent($_SERVER['HTTP_USER_AGENT']);
    if ($ip_carrier == $agent_carrier) {
        return $ip_carrier;
    }
    return FALSE;
}

//+++++++++++++++++++++++++++++
// キャリア判別
//+++++++++++++++++++++++++++++
function get_mail_carrier($mail)
{
    $mail_carrier = "";
    foreach (class_carrer::getCidrAll() as $carrier => $c_domain) {
        foreach ($c_domain as $domain) {
            if (preg_match("/" . preg_quote($domain, "/") . "$/", $mail)) {
                $mail_carrier = $carrier;
                break 2;
            }
        }
    }
    // 携帯のキャリアIPアドレスでない場合はPCからのアクセスとみなす
    if ($mail_carrier == "") {
        $mail_carrier = SYSTEM_CARRIER_PC;
    }
    return $mail_carrier;
}

function is_mobile_mail($mail)
{
    // キャリア取得
    $carrier = get_mail_carrier();
    if ($carrier != "") return true;
    return false;
}

//+++++++++++++++++++++++++++++
// 携帯機種名を取得
//+++++++++++++++++++++++++++++
function get_mobile_device()
{
    $agent = $_SERVER{'HTTP_USER_AGENT'};
    $mobile_carrier = get_mobile_carrier();
    $device = "";
    switch ($mobile_carrier) {
        // DOCOMO
        case SYSTEM_CARRIER_DOCOMO:
            if (strpos($agent, "DoCoMo/1.0") >= 0 && strpos($agent, "/", 11) >= 0) {
                $device = substr($agent, 11, (strpos($agent, "/", 11) - 11));
            } elseif (strpos($agent, "DoCoMo/2.0") >= 0 && strpos($agent, "(", 11) >= 0) {
                $device = substr($agent, 11, (strpos($agent, "(", 11) - 11));
            } else {
                $device = substr($agent, 11);
            }
            break;
        // AU
        case SYSTEM_CARRIER_KDDI:
            $device = substr($agent, (strpos($agent, "-") + 1), (strpos($agent, " ") - strpos($agent, "-") - 1));
            break;
        // SOFTBANK
        case SYSTEM_CARRIER_SOFTBANK:
            $device = $_SERVER{'HTTP_X_JPHONE_MSNAME'};
            break;
        // 未対応
        default:
            return false;
            break;
    }
    return $device;
}

?>