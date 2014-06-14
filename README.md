# PHP LIB
PHP LIB is a rapid development framework for PHP.
support PHP version 4/5

## How To Use
Onry include "init_lib.php" file.
(setup init_lib.php)

    include_once(dir(__FILE__)."/php_lib/init_lib.php");


### Template Engine(class_templates.php)
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

### Database Engine(class_mysql.php)
* SELECT,CREATE,DELETE,INSERT,UPDATE Other

#### php
    // database setup
    $DB = new class_mysql;
    $DB->addDB($G_DB_SERVER,$G_DB_USERNAME,    $G_DB_PASSWORD);
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

## Other Class View
*Social Plugins(Facebook/Twitter)

*Mail(Attatible)

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
