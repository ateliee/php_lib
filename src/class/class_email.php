<?php

/**
 * Class class_emailData
 */
class class_emailData
{
    private $mail;
    private $name;

    /**
     * @param $mail
     * @param null $name
     */
    public function __construct($mail,$name=null)
    {
        $this->mail = $mail;
        $this->name = $name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * メールの表示名を取得
     *
     * @param $form_encoding
     * @param $encoding
     * @return string
     */
    public function getDisplay($encoding,$form_encoding=null)
    {
        if ($this->name) {
            $name = $this->name;
            if($form_encoding == null){
                $form_encoding = mb_internal_encoding();
            }
            if($form_encoding != $encoding){
                $name = mb_convert_encoding($name,$encoding,$form_encoding);
            }
            return mb_encode_mimeheader($name, $encoding, "B", "\n") . " <" . $this->name . ">";
        }
        return $this->mail;
    }
}

/**
 * Class class_emailFileData
 */
class class_emailFileData
{
    static $ATTACHMENT = 'attachment';
    static $INLINE = 'inline';

    private $path;
    private $data;
    private $filename;
    private $mime;
    private $disposition;

    /**
     * @param $path
     * @param string $filename
     * @param string $disposition|attachment:付属物 inline:本文と一緒に表示
     */
    public function __construct($path, $filename = "", $disposition = "attachment")
    {
        if (!($contents = $this->readFile($path))) {
            $contents = $this->getUrlContents($path);
        }
        if ($contents != "") {
            $contents_encoded = chunk_split(base64_encode($contents), 76, "\n"); //エンコードして分割
            $info = pathinfo($path);

            if ($filename == "") {
                $filename = $info["basename"];
            } else {
                $filename .= "." . $info["extension"];
            }
            $this->path = $path;
            $this->filename = $filename;
            $this->data = $contents_encoded;
            $this->mime = $this->checkMimeType($path);
            $this->disposition = $disposition;
        }
    }

    /**
     * 外部ファイルを取得
     *
     * @param $url
     * @return null|string
     */
    private function getUrlContents($url)
    {
        $string = null;
        if($ch = curl_init()){
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            ob_start();
            curl_exec($ch);
            curl_close($ch);

            $string = ob_get_contents();
            ob_end_clean();
        }
        return $string;
    }

    /**
     * @param $path
     * @return null|string
     */
    private function readFile($path)
    {
        if ($fp = @fopen($path, "r")) {
            $contents = "";
            while (!feof($fp)) {
                $contents .= fread($fp, 1024);
            }
            @fclose($fp);
            return $contents;
        }
        return null;
    }

    /**
     * @return mixed|string
     */
    private function checkMimeType()
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
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $this->path);
            finfo_close($finfo);
            return $mimetype;
        } else {
            $info = pathinfo($this->path);
            if (isset($info["extension"]) && isset($mimeTypeList[$info["extension"]])) {
                return $mimeTypeList[$info["extension"]];
            } else if (!function_exists('mime_content_type')) {
                return exec('file -Ib ' . $this->path);
            } else {
                // 非推奨
                return mime_content_type($this->path);
            }
        }
    }

    /**
     * @return mixed|string
     */
    public function getMimeType(){
        return $this->mime;
    }

    /**
     * @return mixed|string
     */
    public function getFilename(){
        return $this->filename;
    }

    /**
     * @return mixed|string
     */
    public function getDisposition(){
        return $this->disposition;
    }

    /**
     * @return mixed|string
     */
    public function getData(){
        return $this->data;
    }
}

/**
 * Class class_mail
 *
 * class_email.phpに以降(仕様変更)
 */
class class_email
{
    private $smtp;
    private $port;

    private $encoding;
    private $system_encoding;
    //var $from_encode = 'SJIS';
    //var $mail_encode = 'ISO-2022-JP';
    private $from;
    private $return;
    private $to;
    private $cc;
    private $bcc;

    private $subject;
    private $body;
    private $files;

    /**
     *
     */
    public function __construct()
    {
        $this->smtp = "localhost";
        $this->port = 25;
        $this->setEncoding("ISO-2022-JP");
        $this->setSystemEncoding(mb_internal_encoding());

        $this->from = null;
        $this->return = null;
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();

        $this->subject = "";
        $this->body = "";
        $this->files = array();
    }

    /**
     * @param $encoding
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * @param $encoding
     * @return $this
     */
    public function setSystemEncoding($encoding)
    {
        $this->system_encoding = $encoding;
        return $this;
    }

    // 文字コード変換
    /*function encoding($val, $to_encoding = "", $from_encoding = "")
    {
        if (($to_encoding != "") && ($from_encoding != "") && ($to_encoding != $from_encoding)) {
            $val = mb_convert_kana($val, "K", $from_encoding);
            $val = mb_convert_encoding($val, $to_encoding, $from_encoding);
        }
        return $val;
    }*/
    /**
     * @param $str
     * @return string
     */
    private function encodeString($str)
    {
        if($this->encoding != $this->system_encoding) {
            $str = mb_convert_kana($str, "K", $this->system_encoding);
            $str = mb_convert_encoding($str, $this->encoding, $this->system_encoding);
        }
        return $str;
    }

    /**
     * @param class_emailData $email
     * @return $this
     */
    public function setFrom(class_emailData $email)
    {
        $this->from = $email;
        return $this;
    }

    /**
     * @return class_emailData
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param class_emailData $email
     * @return $this
     */
    public function setReturnPath(class_emailData $email)
    {
        $this->return = $email;
        return $this;
    }

    /**
     * @return class_emailData
     */
    public function getReturnPath()
    {
        return $this->return;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param class_emailData $email
     * @return $this
     */
    public function addTo(class_emailData $email)
    {
        $this->to[] = $email;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetTo()
    {
        $this->to = array();
        return $this;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param class_emailData $email
     * @return $this
     */
    public function addCC(class_emailData $email)
    {
        $this->cc[] = $email;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetCC()
    {
        $this->cc = array();
        return $this;
    }

    /**
     * @return array
     */
    public function getCC()
    {
        return $this->cc;
    }

    /**
     * @param class_emailData $email
     * @return $this
     */
    public function addBCC(class_emailData $email)
    {
        $this->bcc[] = $email;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetBCC()
    {
        $this->bcc = array();
        return $this;
    }

    /**
     * @return array
     */
    public function getBCC()
    {
        return $this->bcc;
    }

    /*
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
    }*/

    /**
     * ファイルの添付
     *
     * @param $path
     * @param string $filename
     * @param string $disposition
     * @return bool
     */
    public function addFile($path, $filename = "", $disposition = "attachment")
    {
        if ($path != "") {
            $this->files[] = new class_emailFileData($path,$filename,$disposition);
        }
        return false;
    }

    /**
     *
     */
    public function deleteFiles()
    {
        $this->files = array();
        return $this;
    }

    /**
     * メール送信
     *
     * @return bool
     */
    function send()
    {
        $org_encoding = mb_internal_encoding();

        mb_language("Japanese");
        //mb_language("ja");
        mb_internal_encoding($this->encoding);
        // WindowsではSMTPのMAIL FROM（エンベロープFrom）に使われる
        //ini_set("smtp_port", $this->port);
        //ini_set("SMTP", $this->smpt);
        ini_set("sendmail_from", $this->getFrom()->getMail());
        //ini_set("sendmail_from", mb_encode_mimeheader($this->subject,$this->mail_encode,"B","\n"));

        $hvaluelist = array();
        // メールヘッダー作成
        $hvaluelist['From'] = $this->getFrom()->getDisplay($this->encoding,$this->system_encoding);
        // To
        $maillist = array();
        foreach ($this->to as $t) {
            $maillist[] = $t->getDisplay($this->encoding,$this->system_encoding);
        }
        $to = implode(",", $maillist);
        //$hvaluelist['To'] = $to;
        // CC
        $maillist = array();
        foreach ($this->cc as $t) {
            $maillist[] = $t->getDisplay($this->encoding,$this->system_encoding);
        }
        if (count($maillist) > 0) {
            $hvaluelist['Cc'] = implode(",", $maillist);
        }
        // BCC
        $maillist = array();
        foreach ($this->bcc as $t) {
            $maillist[] = $t->getDisplay($this->encoding,$this->system_encoding);
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
            $body .= "Content-Type: text/plain; charset=" . $this->encoding . "\n";
            $body .= "Content-Transfer-Encoding: 7bit\n\n";
            $body .= $this->encodeString($this->body) . "\n\n";
            $body .= "--" . $boundary . "\n";
            foreach ($this->files as $file) {
                $body .= "Content-Type: " . $file->getMimeType() . "; name=\"" . $file->getFilename() . "\"\n";
                $body .= "Content-Transfer-Encoding: base64\n";
                $body .= "Content-Disposition: " . $file->getDisposition() . "; filename=\"" . $file->getFilename() . "\"\n\n";
                $body .= $file->getData() . "\n";
                $body .= "--" . $boundary . "--\n";
            }
        } else {
            $hvaluelist["Content-Type"] = "text/plain; charset=" . $this->encoding;
            $hvaluelist["Content-Transfer-Encoding"] = "7bit";
            $body = $this->encodeString($this->body);
        }
        if ($this->return) {
            $hvaluelist['Return-Path'] = $this->return->getMail();
        } else {
            $hvaluelist['Return-Path'] = $this->from->getMail();
        }
        $hvaluelist['Reply-To'] = $this->from->getMail();
        $hvaluelist["X-Mailer"] = "PHP/" . phpversion() . "";
        $hvaluelist["MIME-version"] = "1.0";
        $param = "-f" . $this->from->getMail();
        if ($this->return != "") {
            $param = "-f" . $this->return->getMail();
        }
        $header = array();
        foreach ($hvaluelist as $key => $val) {
            $header[] = ucfirst($key) . ": " . $val;
        }
        $header = implode("\n", $header);
        $subject = mb_encode_mimeheader($this->encodeString($this->subject), $this->encoding, "B", "\n");

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
