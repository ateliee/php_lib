# PHP LIB
![image](/example/images/icon.png)

PHP LIB is a rapid development framework for PHP.
support PHP version 4/5

## How To Use
Onry include "init_lib.php" file.
(setup init_lib.php)

    include_once(dir(__FILE__)."/php_lib/init_lib.php");


## Example
* [Template Engine](#tpl)
* [Database Engine](#db)
* [Mail Engine](#mail)
* [Functions](#func)

### <a name="tpl">Template Engine(class_templates.php)
* set variable(string,integer,double,associative array, array other)
* nest support.
* if,else,else if,foreach,for
* function support

#### php
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

#### html(index.html)
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

### <a name="db">Database Engine(class_mysql.php)
* SELECT,CREATE,DELETE,INSERT,UPDATE Other

#### php
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

### <a name="mail">Mail Engine(class_mail.php)
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

### <a name="func">Functions(/func/)
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
    

## Other Class View
*Social Plugins(Facebook/Twitter)

*iPhone/Android Push(push notification)

*Blog Post(Wordpress/Ameba/FC2)

*Create Calender

*Cookie Manager

*Session Manager

*File Manager

*Flickr API

*FTP Manager

*IMAP Class

*Mobile Class

*PDF Class

*Rakuten API

*RSS Class

*wiki API

*XML Class

*YAhoo API

*Zip Class



**These are some functions.
More convenient functions are varied.
====================================
I recommend it to the person whom I want to easily employ without minding a version of php.
==================================================
