<?php
//============================================
// class_imap.php
//============================================
define("IMAP_FLAG_SEEN",0x01);          // 既読
define("IMAP_FLAG_ANSWERED",0x02);      // 返信済み
define("IMAP_FLAG_DELETED",0x04);       // 削除
define("IMAP_FLAG_DRAFT",0x08);         // 草稿
define("IMAP_FLAG_RECENT",0x10);        // 新規
define("IMAP_FLAG_FLAGGED",0x20);       // 特別な注意

//+++++++++++++++++++++++++++++
// IMAPクラス
//+++++++++++++++++++++++++++++
class class_imap{
    var     $mbox = null;
    var     $host = '';
    var     $port = 110;
    var     $flag = "/pop3/notls";
    var     $mailbox_name = "INBOX";
    var     $username = '';
    var     $password = '';
    // 内部データ
    var     $htmlmsg;
    var     $plainmsg;
    var     $charset;
    var     $attachments;
    //-----------------------------
    // IMAPオープン
    //-----------------------------
    function open($host,$username,$password){
        // 初期化
        $this->host = "";
        $this->username = "";
        $this->password = "";
        // メールボックスオープン
        $this->mbox = @imap_open("{".$host.":".$this->port.$this->flag."}".$this->mailbox_name, $username, $password);
        if($this->mbox){
            $this->host = $username;
            $this->username = $username;
            $this->password = $password;
            return true;
        }
        return false;
    }
    // メールヘッダー文字列変換
    function convert_mail_str($str,$to="",$from=""){
        $str = mb_decode_mimeheader($str);
        if($to != "" && $from != ""){
                $str = mb_convert_encoding($str, $to, $from);
        }else if($to != ""){
                $str = mb_convert_encoding($str, $to, "auto");
        }
        return $str;
    }
    // メールの取得
    function search($criteria,$options = SE_FREE){
        if($this->mbox){
            return imap_search($this->mbox,$criteria,$options);
        }
        return false;
    }
    // メールの解析
    function getmsg($mid){
        // the message may in $htmlmsg, $plainmsg, or both
        $this->htmlmsg = "";
        $this->plainmsg = "";
        $this->charset = '';
        $this->attachments = array();
        
        // HEADER
        $h = imap_header($this->mbox,$mid);
        // add code here to get date, from, to, cc, subject...
        
        // BODY
        $s = imap_fetchstructure($this->mbox,$mid);
        if(!isset($s->parts) || !$s->parts){
                $this->getpart($mid,$s,0);  // no part-number, so pass 0
        // multipart: iterate through each part
        }else {
                foreach ($s->parts as $partno0 => $p){
                        $this->getpart($mid,$p,$partno0 + 1);
                }
        }
        // データを返す
        $result = array();
        $result["header"] = $h;
        $result["html"] = $this->htmlmsg;
        $result["plain"] = $this->plainmsg;
        $result["charset"] = $this->charset;
        $result["attachments"] = $this->attachments;
        return $result;
    }
    // $partno = '1', '2', '2.1', '2.1.3', etc if multipart, 0 if not multipart
    function getpart($mid,$p,$partno) {
        // DECODE DATA(multipart or not multipart
        $data = ($partno) ? imap_fetchbody($this->mbox,$mid,$partno) : imap_body($this->mbox,$mid);
        // QUOTED-PRINTABLE
        if ($p->encoding == 4){
                $data = quoted_printable_decode($data);
        // BASE64
        }elseif ($p->encoding == 3){
                $data = base64_decode($data);
        }
        // no need to decode 7-bit, 8-bit, or binary
        
        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        $params = array();
        if ($p->ifparameters && $p->parameters){
                foreach ($p->parameters as $x){
                        $params[ strtolower( $x->attribute ) ] = $x->value;
                }
        }
        // Content-disposition MIME ヘッダ
        if ($p->ifdparameters && $p->dparameters){
                foreach ($p->dparameters as $x){
                        $params[ strtolower( $x->attribute ) ] = $x->value;
                }
        }
        //d(imap_fetchstructure($this->mbox,$mid));
        // ATTACHMENT
        // Any part with a filename is an attachment,
        // so an attached text file (type 0) is not mistaken as the message.
        if(isset($params['filename']) || isset($params['name'])){
                // filename may be given as 'Filename' or 'Name' or both
                $filename = isset($params['filename'])? $params['filename'] : $params['name'];
                // filename may be encoded, so see imap_mime_header_decode()
                $this->attachments[$filename] = array();  // this is a problem if two files have same name
                $this->attachments[$filename]["data"] = $data;
                if(isset($p->id)){
                        $this->attachments[$filename]["id"] = $p->id;
                }
                if(isset($p->bytes)){
                        $this->attachments[$filename]["bytes"] = $p->bytes;
                }
                if(isset($p->description)){
                        $this->attachments[$filename]["description"] = $p->description;
                }
                if(isset($p->subtype)){
                        $this->attachments[$filename]["subtype"] = $p->subtype;
                }
        // TEXT
        }elseif ($p->type == 0 && $data) {
                // Messages may be split in different parts because of inline attachments,
                // so append parts together with blank row.
                if (strtolower($p->subtype) == 'plain'){
                        $this->plainmsg .= trim($data) ."\n\n";
                }else{
                        $this->htmlmsg .= $data ."<br><br>";
                }
                $this->charset = $params['charset'];  // assume all parts are same charset
        // EMBEDDED MESSAGE
        // Many bounce notifications embed the original message as type 2,
        // but AOL uses type 1 (multipart), which is not handled here.
        // There are no PHP functions to parse embedded messages,
        // so this just appends the raw source to the main message.
        }elseif ($p->type == 2 && $data) {
                $this->plainmsg .= trim($data) ."\n\n";
        }
        
        // SUBPART RECURSION
        if (isset($p->parts) && $p->parts) {
                foreach ($p->parts as $partno0 => $p2){
                        $this->getpart($mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
                }
        }
    }
    /*
    // メールの解析
    function parseparts($id){
        if($this->mbox){
            $result = array();
            // メールの内容取得
            $m = imap_fetchstructure($this->mbox ,$id);
            // マルチパート時のみ処理
            if(isset($m->parts) && (count($m->parts) > 0)){
                foreach ($m->parts as $partno => $partarr){
                    // データ読み出し
                    $tmp_file = imap_fetchbody($this->mbox ,$id ,$partno+1);
                    // decode if base64
                    if($partarr->encoding == 3){
                        $tmp_file = base64_decode($tmp_file);
                    }
                    // decode if quoted printable
                    if($partarr->encoding == 4){
                        $tmp_file = quoted_printable_decode($tmp_file);
                    }
                    // textではない時
                    if($partarr->type != 0){
                        // ファイル取得
                        $filename = "";
                        // if there are any dparameters present in this part
                        if(isset($partarr->dparameters) && count($partarr->dparameters) > 0){
                            foreach($partarr->dparameters as $dparam){
                                if((strtoupper($dparam->attribute)=='NAME') || (strtoupper($dparam->attribute)=='FILENAME')){
                                    $filename = $dparam->value;
                                }
                            }
                            if($filename == ""){
                                // if there are any parameters present in this part
                                if (count($partarr->parameters)>0){
                                    foreach ($partarr->parameters as $param){
                                        if((strtoupper($param->attribute)=='NAME') || (strtoupper($param->attribute)=='FILENAME')){
                                            $filename = $param->value;
                                        }
                                    }
                                }
                            }
                        }
                        // ファイルがある場合
                        if($filename != ""){
                            $value = array();
                            $value["type"] = strtoupper($partarr->subtype);
                            $value["filename"] = $filename;
                            $value["binary"] = $tmp_file;
                            $result[$partno] = $value;
                        }
                    // textデータ
                    }else if($partarr->type == 0){
                        if (strtoupper($partarr->subtype)=='PLAIN'){
                        }else if (strtoupper($partarr->subtype)=='HTML'){
                        }
                        
                        $value = array();
                        $value["type"] = strtoupper($partarr->subtype);
                        $value["text"] = $tmp_file;
                        $result[$partno] = $value;
                    }
                }
            }
            return $result;
        }
        return false;
    }*/
    // メールボックスの情報を取得
    function check(){
        if($this->mbox){
            return @imap_check($this->mbox);
        }
        return false;
    }
    // メール個数を取得
    function num_msg(){
        if($this->mbox){
            return imap_num_msg($this->mbox);
        }
        return false;
    }
    // 新着メッセージを取得
    function num_recent(){
        if($this->mbox){
            return imap_num_recent($this->mbox);
        }
        return false;
    }
    // メッセージの削除
    function delete($id,$options=0){
        if($this->mbox){
             return imap_delete($this->mbox,$id,$options);
        }
        return false;
    }
    // メッセージの移動
    function mail_move ($msglist, $mailbox ,$options = 0){
        if($this->mbox){
             return imap_mail_move($this->mbox,$msglist,$mailbox,$options);
        }
        return false;
    }
    // フラグセット
    function setflag($id,$flag){
        $flaglist = array();
        // 既読
        if($flag & IMAP_FLAG_SEEN){
            $flaglist[] = "\\Seen";
        }
        // 返信済み
        if($flag & IMAP_FLAG_ANSWERED){
            $flaglist[] = "\\Answered";
        }
        // 削除
        if($flag & IMAP_FLAG_DELETED){
            $flaglist[] = "\\Deleted";
        }
        // 草稿
        if($flag & IMAP_FLAG_DRAFT){
            $flaglist[] = "\\Draft";
        }
        // 新規
        if($flag & IMAP_FLAG_RECENT){
            $flaglist[] = "\\Recent";
        }
        // 特別な注意
        if($flag & IMAP_FLAG_FLAGGED){
            $flaglist[] = "\\Flagged";
        }
        if(count($flaglist) > 0){
            return @imap_setflag_full($this->mbox, $id, implode(" ",$flaglist));
        }
        return false;
    }
    // 削除用にマークされたすべてのメッセージを削除する
    function expunge(){
        @imap_expunge($this->mbox);
    }
    // クローズ
    function close(){
        // POP3サーバ切断
        if($this->mbox){
            @imap_close($this->mbox);
            $this->mbox = null;
        }
    }
    // エラーを返す
    function errors(){
        return @imap_errors();
    }
    function alerts(){
        return @imap_alerts();
    }
    function last_error(){
        return @imap_last_error();
    }
}

?>
