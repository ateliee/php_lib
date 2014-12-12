<?php
//============================================
// class_mail.php
//============================================
class class_mail
{
    var $mail_smpt = "localhost";
    var $mail_port = 25;

    //var $from_encode = 'Shift-JIS';
    var $from_encode = 'SJIS';
    var $mail_encode = 'ISO-2022-JP';

    var $from = "";
    var $fromName = "";
    var $subject = "";
    var $body = "";
    var $tolist = array();
    var $cclist = array();
    var $bcclist = array();
    var $returnPath = "";
    var $files = array();

    // コンストラクタ
    function class_mail()
    {
    }

    // 文字コード変換
    function encoding($val, $to_encoding = "", $from_encoding = "")
    {
        if (($to_encoding != "") && ($from_encoding != "") && ($to_encoding != $from_encoding)) {
            $val = mb_convert_kana($val, "K", $from_encoding);
            $val = mb_convert_encoding($val, $to_encoding, $from_encoding);
        }
        return $val;
    }

    // 外部ファイルを取得
    function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $string = ob_get_contents();
        ob_end_clean();
        return $string;
    }

    // メールの表示名を取得
    function getMailDisplay($mail, $name = "")
    {
        if ($name != "") {
            return mb_encode_mimeheader($name, $this->mail_encode, "B", "\n") . " <" . $mail . ">";
        }
        return $mail;
    }

    // 送信元を設定
    function setFrom($val)
    {
        $this->from = $val;
        $this->from = mb_convert_encoding($this->from, $this->mail_encode, $this->from_encode);
    }

    function setFromName($val, $to_encoding = "", $from_encoding = "")
    {
        $this->fromName = $this->encoding($val, $to_encoding, $from_encoding);
        $this->fromName = $this->encoding($this->fromName, $this->mail_encode, $this->from_encode);
    }

    function setFromMail($val, $name = "", $to_encoding = "", $from_encoding = "")
    {
        $this->setFrom($val, $to_encoding, $from_encoding);
        $this->setFromName($name, $to_encoding, $from_encoding);
    }

    // Return-Pathを設定
    function setReturnPath($val)
    {
        $this->returnPath = $val;
        $this->returnPath = mb_convert_encoding($this->returnPath, $this->mail_encode, $this->from_encode);
    }

    // 件名を設定
    function setSubject($val, $to_encoding = "", $from_encoding = "")
    {
        $this->subject = $this->encoding($val, $to_encoding, $from_encoding);
        $this->subject = $this->encoding($this->subject, $this->mail_encode, $this->from_encode);
    }

    // 本文を設定
    function setBody($val, $to_encoding = "", $from_encoding = "")
    {
        // 改行コード置換
        $val = str_replace(array("\r\n", "\r"), "\n", $val);
        $this->body = $this->encoding($val, $to_encoding, $from_encoding);
        $this->body = $this->encoding($this->body, $this->mail_encode, $this->from_encode);
    }

    // 送信先を設定
    function setTo($val, $to_encoding = "", $from_encoding = "")
    {
        $v = $this->encoding($val, $to_encoding, $from_encoding);
        $v = $this->encoding($v, $this->mail_encode, $this->from_encode);
        $this->tolist[0]["mail"] = $v;
    }

    function setToName($val, $to_encoding = "", $from_encoding = "")
    {
        $v = $this->encoding($val, $to_encoding, $from_encoding);
        $v = $this->encoding($v, $this->mail_encode, $this->from_encode);
        $this->tolist[0]["name"] = $v;
    }

    function addToMail($val, $name = "", $to_encoding = "", $from_encoding = "")
    {
        $v = $this->encoding($val, $to_encoding, $from_encoding);
        $v = $this->encoding($v, $this->mail_encode, $this->from_encode);
        $vn = $this->encoding($name, $to_encoding, $from_encoding);
        $vn = $this->encoding($vn, $this->mail_encode, $this->from_encode);
        $this->tolist[] = array("mail" => $v, "name" => $vn);
    }

    function resetToMail()
    {
        $this->tolist = array();
    }

    // CCを追加
    function addCCMail($val, $name = "", $to_encoding = "", $from_encoding = "")
    {
        $v = $this->encoding($val, $to_encoding, $from_encoding);
        $v = $this->encoding($v, $this->mail_encode, $this->from_encode);
        $vn = $this->encoding($name, $to_encoding, $from_encoding);
        $vn = $this->encoding($vn, $this->mail_encode, $this->from_encode);
        $this->cclist[] = array("mail" => $v, "name" => $vn);
    }

    function resetCCMail()
    {
        $this->cclist = array();
    }

    // BCCを追加
    function addBCCMail($val, $name = "", $to_encoding = "", $from_encoding = "")
    {
        $v = $this->encoding($val, $to_encoding, $from_encoding);
        $v = $this->encoding($v, $this->mail_encode, $this->from_encode);
        $vn = $this->encoding($name, $to_encoding, $from_encoding);
        $vn = $this->encoding($vn, $this->mail_encode, $this->from_encode);
        $this->bcclist[] = array("mail" => $v, "name" => $vn);
    }

    function resetBCCMail()
    {
        $this->bcclist = array();
    }

    function file_read($path)
    {
        if ($fp = @fopen($path, "r")) {
            //$size = filesize($path);
            //$size = exec('stat -c %s '. escapeshellarg ($path));
            $contents = "";
            while (!feof($fp)) {
                $contents .= fread($fp, 1024);
            }
            @fclose($fp);
            return $contents;
        }
        return null;
    }

    function getMimeType($path)
    {
        $mimeTypeList = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jp2' => 'image/jp2',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ai' => 'application/postscript',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'tsv' => 'text/tab-separated-values',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pdf' => 'application/pdf',
            'xdw' => 'application/vnd.fujixerox.docuworks',
            'htm' => 'text/html',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'text/javascript',
            'hdml' => 'text/x-hdml',
            'mp3' => 'audio/mpeg',
            'mp4' => 'audio/mp4',
            'wav' => 'audio/x-wav',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mmf' => 'application/x-smaf',
            'mpg' => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'wmv' => 'video/x-ms-wmv',
            'swf' => 'application/x-shockwave-flash',
            '3g2' => 'video/3gpp2',
            'zip' => 'application/zip',
            'lha' => 'application/x-lzh',
            'lzh' => 'application/x-lzh',
            'tar' => 'application/x-tar',
            'tgz' => 'application/x-tar',
            //'tar'   => 'application/octet-stream',
            //'tgz'   => 'application/octet-stream',
        );
        if (function_exists("finfo_file")) {
            $mimetype = finfo_file($finfo, $path);
            finfo_close($finfo);
            return $mimetype;
        } else {
            $info = pathinfo($path);
            if (isset($info["extension"]) && isset($mimeTypeList[$info["extension"]])) {
                return $mimeTypeList[$info["extension"]];
            } else if (!function_exists('mime_content_type')) {
                return exec('file -Ib ' . $path);
            } else {
                // 非推奨
                return mime_content_type($path);
            }
        }
    }

    // ファイルの添付
    function addFile($path, $filename = "", $disposition = "attachment")
    {
        if ($path != "") {
            $contents = null;
            if (!($contents = $this->file_read($path))) {
                $contents = $this->file_get_contents_curl($path);
            }
            if ($contents != "") {
                $contents_encoded = chunk_split(base64_encode($contents), 76, "\n"); //エンコードして分割
                $info = pathinfo($path);

                if ($filename == "") {
                    $filename = $info["basename"];
                    $filename = mb_convert_encoding($filename, $this->mail_encode, $this->from_encode);
                } else {
                    $filename = mb_convert_encoding($filename, $this->mail_encode, $this->from_encode);
                    $filename .= "." . $info["extension"];
                }
                $file = array();
                $file["name"] = $filename;
                $file["data"] = $contents_encoded;
                $file["mime"] = $this->getMimeType($path);
                $file["disposition"] = $disposition; // attachment:付属物 inline:本文と一緒に表示
                $this->files[] = $file;
            }
        }
        return false;
    }

    function setFiles($files)
    {
        $this->deleteFiles();
        foreach ($files as $file) {
            $this->addFile($file["path"], isset($file["name"]) ? $file["name"] : "");
        }
    }

    function deleteFiles()
    {
        $this->files = array();
    }

    // メール送信
    function send()
    {
        $org_encoding = mb_internal_encoding();

        mb_language("Japanese");
        //mb_language("ja");
        mb_internal_encoding($this->mail_encode);
        // WindowsではSMTPのMAIL FROM（エンベロープFrom）に使われる
        //ini_set("smtp_port", $mail_port);
        //ini_set("SMTP", $mail_smpt);
        ini_set("sendmail_from", $this->from);
        //ini_set("sendmail_from", mb_encode_mimeheader($this->subject,$this->mail_encode,"B","\n"));

        $hvaluelist = array();
        // メールヘッダー作成
        $hvaluelist['From'] = $this->getMailDisplay($this->from, $this->fromName);
        // To
        $maillist = array();
        foreach ($this->tolist as $t) {
            $maillist[] = $this->getMailDisplay($t["mail"], $t["name"]);
        }
        $to = implode(",", $maillist);
        //$hvaluelist['To'] = $to;
        // CC
        $maillist = array();
        foreach ($this->cclist as $t) {
            $maillist[] = $this->getMailDisplay($t["mail"], $t["name"]);
        }
        if (count($maillist) > 0) {
            $hvaluelist['Cc'] = implode(",", $maillist);
        }
        // BCC
        $maillist = array();
        foreach ($this->bcclist as $t) {
            $maillist[] = $this->getMailDisplay($t["mail"], $t["name"]);
        }
        if (count($maillist) > 0) {
            $hvaluelist['Bcc'] = implode(",", $maillist);
        }
        //$hvaluelist["Message-Id"] = "<".md5(uniqid(microtime()))."@ドメイン">";
        if (count($this->files) > 0) {
            $boundary = md5(uniqid(rand())); //バウンダリー文字（パートの境界）
            //$boundary = "_Boundary_" . uniqid(rand(1000,9999) . '_') . "_";
            $hvaluelist["Content-Type"] = "multipart/mixed; boundary=\"" . $boundary . "\"";
            $hvaluelist["Content-Transfer-Encoding"] = "7bit";

            $body = "";
            $body .= "--" . $boundary . "\n";
            $body .= "Content-Type: text/plain; charset=" . $this->mail_encode . "\n";
            $body .= "Content-Transfer-Encoding: 7bit\n\n";
            $body .= $this->body . "\n\n";
            $body .= "--" . $boundary . "\n";
            foreach ($this->files as $file) {
                $body .= "Content-Type: " . $file["mime"] . "; name=\"" . $file["name"] . "\"\n";
                $body .= "Content-Transfer-Encoding: base64\n";
                $body .= "Content-Disposition: " . $file["disposition"] . "; filename=\"" . $file["name"] . "\"\n\n";
                $body .= $file["data"] . "\n";
                $body .= "--" . $boundary . "--\n";
            }
        } else {
            $hvaluelist["Content-Type"] = "text/plain; charset=" . $this->mail_encode;
            $hvaluelist["Content-Transfer-Encoding"] = "7bit";
            $body = $this->body;
        }
        if ($this->returnPath != "") {
            $hvaluelist['Return-Path'] = $this->returnPath;
        } else {
            $hvaluelist['Return-Path'] = $this->from;
        }
        $hvaluelist['Reply-To'] = $this->from;
        $hvaluelist["X-Mailer"] = "PHP/" . phpversion() . "";
        $hvaluelist["MIME-version"] = "1.0";
        $param = "-f" . $this->from;
        if ($this->returnPath != "") {
            $param = "-f" . $this->returnPath;
        }
        $header = array();
        foreach ($hvaluelist as $key => $val) {
            $header[] = ucfirst($key) . ": " . $val;
        }
        $header = implode("\n", $header);
        $subject = mb_encode_mimeheader($this->subject, $this->mail_encode, "B", "\n");

        // 送信（第1引数はSMTPのRCPT TO（エンベロープTO）にも使われる）
        //if (mb_send_mail($this->to, $subject, $body, $header)) {
        $result = false;
        if (@mail($to, $subject, $body, $header, $param)) {
            $result = true;
        }
        mb_internal_encoding($org_encoding);
        return $result;
    }
}
