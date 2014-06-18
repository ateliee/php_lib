# PHP LIB
![image](/example/images/icon.png)

PHP LIB is a rapid development framework for PHP.
support PHP version 4/5

## Download
[Version 1.0](https://github.com/ateliee/php_lib/archive/master.zip)

## How To Use
Onry include "init_lib.php" file.
(setup init_lib.php)

``` php
include_once(dir(__FILE__)."/php_lib/init_lib.php");
```

## Example
* [Template Engine](#tpl)
* [Database Engine](#db)
* [Mail Engine](#mail)
* [Session Manager](#session)
* [Cookie Manager](#cookie)
* [FTP Maganer](#FTP)
* [File Manager](#file)
* [APNS Server Side](#apns)
* [GCM Server Side](#gcm)
* [Functions](#func)
* [Define](#define)

### <a name="tpl">Template Engine(class_templates.php)
* set variable(string,integer,double,associative array, array other)
* nest support.
* if,else,else if,foreach,for
* function support

``` php
$TPL = new class_template();
// load template file
$TPL->load("index.html");
// set template parts file
$TPL->setIncludeFile("Parts_File","parts.html");
// set template value
$value = array("name" => "sample");
$TPL->assign("human",$value);
$value = "test";
$TPL->assign("name",$value);

// set asssociative array
$TPL->assign_vars(array("test" => "string","ary" => array(1 => "test")));
// get template to string
$html_str = $TPL->get_display_template();
```

``` html(index.html)
<!-- set template parts file -->
<?include file='Parts_File'?>
<!-- 'name => sample' -->
<?foreach $human as $key => $item?>
<?$key?> => <?$item?>
<?/foreach?>
<!-- 'name is test' -->
name is <?$name?>
<!-- 'name is test' -->
<?if( $name == 'test' )?>
name is test
<?else?>
name is not test
<?/if?>

<!-- escape string -->
name is <?htmlentities($name)?>
name is <?escape($name)?>
name is <?htmlspecialchars($name)?>
name is <?escape_br($name)?>
name is <?quotes($name)?>
name is <?urlencode($name)?>
<!-- is_***() -->
name is <?is_array($name)?>
name is <?is_numeric($name)?>
name is <?is_string($name)?>
<!-- format funciton -->
name is <?number_format($name)?>
name is <?count($name)?>
```

### <a name="db">Database Engine(class_mysql.php)
* SELECT,CREATE,DELETE,INSERT,UPDATE Other
* PHP5 is MYSQLI,PHP4 is mysql_***() function

``` php
// database setup
$DB = new class_mysql;
$DB->addDB($G_DB_SERVER,$G_DB_USERNAME,$G_DB_PASSWORD);
$DB->setCurrentDB(0);
$DB->get()->setDBName($G_DB_NAME);
$DB->get()->setCharset($G_DB_ENCODE);
// connect auto
$DB->setAutoConnect(true);
// debug mode
$DB->setDebug(true);

// sql create
$sql = "SELECT COUNT(*) FROM `test` ";
// query
$DB->query($sql);
if($DB->numRows() > 0){
    $sv = $DB->fetchArray();
}
```

### <a name="mail">Mail Engine(class_mail.php)
``` php
$MAIL = new class_mail;
// setting
$MAIL->setFromName("sample",SYSTEM_MAIL_ENCODE,SYSTEM_PHP_ENCODE);
$MAIL->setFrom("example@sample.com");
$MAIL->setSubject("sample mail",SYSTEM_MAIL_ENCODE,SYSTEM_PHP_ENCODE);
$MAIL->setBody($body);
$MAIL->setTo("test@sample.com");
$MAIL->addCCMail("test2@sample.com");
$MAIL->addBCCMail("test2@sample.com");
// attribe file
$MAIL->addFile("/path/to/file.jpg","sample.jpg");
// send mail
$MAIL->send();
```

### <a name="session">Session Manager(class_session.php)

``` php
$SESSION = new class_session;
// settiong namespace
$SESSION->start();
$SESSION->SELECT($NAMESCAPE);

$SESSION->set($NAME,$VALUE);
$value = $SESSION->get($NAME);
```

### <a name="cookie">Cookie Manager(class_cookie.php)

``` php
$COOKIE = new class_cookie;
$COOKIE->init($COOKIENAME,$PERIOD);
$COOKIE->set($NAME,$VALUE);
$value = $COOKIE->get($NAME);
```

### <a name="ftp">FTP Manager(class_ftp.php)

``` php
$FTP = new class_ftp;
$FTP->ftp_server = "localhost";
$FTP->user_name = "user_name";
$FTP->user_pass = "password";
$FTP->pasv(true);
// connect FTP
$result = $FTP->connect();
// connect SSL
$result = $FTP->ssl_connect();
// close
$FTP->close();
```

### <a name="file">File Manager(class_file.php)

``` php
$FILE = new class_file;
$FILE->setDir($DIR);
// read
$FILE->readArray($filename);
$FILE->readAll($filename);
$FILE->readDirAll($filename);
```

### <a name="apns">APNS Server Side(class_apns.php)

* APNS is iPhone App Push Notifications On Server Side.
* single push support(multible is next update)

``` php
$APNS = new class_apns;
// setting
$APNS->init($APNS_ID,$APNS_PASS);
// set debug mode
$APNS->setDebug(true);
// send device token
$APNS->setDeviceToken($token);
// set message(options)
$APNS->setBody(array(
    'alert' => $message,
    "badge" => 1,
    'sound' => 'default',
    'content-available' => 1
));
// send
$APNS->pushMessage();
```

### <a name="gcm">GCM Server Side(class_gcm.php)
* single and multible Push Notifications

``` php
$GCM = new class_gcm;
// setting
$GCM->init($API_KEY);
// send device
$GCM->setRegistrationIDs($IDS);
// options
$GCM->setData(array(
    'collapse_key' => "update",  //  オンライン復活時に表示する文字列
    'time_to_live' => 60 * 60 * 24 * 28,  // クライアント端末がオフラインであった場合に、いつまでメッセージを保持するか。秒単位で指定。
    'delay_while_idle' => false,  // 端末がidle時はactiveになるまで送信を待つ
    'dry_run' => false,  //  true:実際にはメッセージを送信しない。開発時のテスト用。
    'data' => array('message' => $message)       // ペイロード
));
// send message
$GCM->pushMessage();
```

### <a name="func">Functions(/func/)
``` php
// user agent check
is_mobile_docomo_agent();
is_mobile_kddi_agent();
is_mobile_softbank_agent();
is_mobile_willcom_agent();
is_iphone_agent();
is_ipad_agent();
is_ipod_agent();
is_android_agent();
is_tablet_agent();
// ip to mobile carrier
$carrier_id = get_ip_carrier();
// Basic Auth For PHP
AuthenticateUser(array("sample" => "sample"));
// checked put html
$html = htmlChecked(true);    // output checked="checked"
$html = htmlDisabled(true);    // output disabled="disabled"
$html = htmlReadonly(true);    // output readonly="readonly"
// select options output
$options = makeValueOpts(array("1" => "Tokyo"));
// pnkz html making
$pnkz = array();
$pnkz[] = makePnkz("home","http://sample.com/");
$pnkz[] = makePnkz("about","http://sample.com/about/");
$html = getPnkz($pnkz,array("mode" => "list"));
// function
d($value);   // <pre> and print_r()
h($value);   // html htmlspecialchars()
m($value);   // mysql mysql_real_escape_string()
m_pathinfo($value);  // pathinfo for php4,5
// array function
$first_key = get_first_key($ary);   // get array first key
$first_val = get_first_value($ary);   // get array first value
$last_key = get_last_key($ary);   // get array last key
$last_val = get_last_value($ary);   // get array last value
```

### <a name="define">Define(/conf/)
``` php
$G_SYSTEM_PREF;   // Japan Ken
$G_SYSTEM_CONSTELLATIONS;    // constellation
$G_SYSTEM_BLOOD;   // blood
$G_SYSTEM_COUNTRY;   // country
SYSTEM_CARRIER_PC,SYSTEM_CARRIER_DOCOMOSYSTEM_CARRIER_AU,SYSTEM_CARRIER_KDDI,SYSTEM_CARRIER_SOFTBANK,SYSTEM_CARRIER_WILLCOM;   // carrier
SYSTEM_TIME_1MINUTES;   // 1 minutes timestamp(60)
SYSTEM_TIME_1HOUR;   // 1 minutes timestamp(3600)
etc.
```

## Other Class View

* Social Plugins(Facebook/Twitter)
* Blog Post(Wordpress/Ameba/FC2)
* Create Calender
* Flickr API
* IMAP Class
* Mobile Class
* PDF Class
* Rakuten API
* RSS Class
* wiki API
* XML Class
* YAhoo API
* Zip Class

## Useing Library

* [upgrade.php](http://include-once.org/p/upgradephp/)
* [QRcode Perl CGI & PHP scripts](http://www.swetake.com/qrcode/qr_cgi.html)
* [facebook-sdk](https://developers.facebook.com/docs/reference/php/4.0.0)
* [FPDI](http://www.setasign.com/products/fpdi/about/)
* [magpierss](http://magpierss.sourceforge.net/)
* [google-api-php-client](https://github.com/google/google-api-php-client)
* [OpenSlopeOne](https://code.google.com/p/openslopeone/)
* [PEAR](http://pear.php.net/manual/ja/)
* [qdmail_receiver](http://hal456.net/qdmail_rec/)
* [simplehtmldom](http://simplehtmldom.sourceforge.net/)
* [TCPDF](http://www.tcpdf.org/)
* [twitteroauth](https://github.com/abraham/twitteroauth)


**These are some functions.
More convenient functions are varied. 
I recommend it to the person whom I want to easily employ without minding a version of php.**
