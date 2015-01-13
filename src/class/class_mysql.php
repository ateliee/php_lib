<?php

define("MYSQL_MODE_MYSQL",1);
define("MYSQL_MODE_MYSQLI",2);
define("MYSQL_MODE_PDO",3);

class class_mysql_column_obj{
    private $table;

    function __construct()
    {
        $this->table = null;
    }


    /**
     * @param class_mysql_table $table
     */
    public function setTable(class_mysql_table $table)
    {
        $this->table = $table;
    }

    /**
     * @return class_mysql_table|null
     */
    public function getTable()
    {
        return $this->table;
    }
}

/**
 * Class class_mysql_column
 */
class class_mysql_column extends class_mysql_column_obj{
    static $CHAR = 'CHAR';
    static $VARCHAR = 'VARCHAR';
    static $TEXT = 'TEXT';
    static $DATE = 'DATE';
    static $DATETIME = 'DATETIME';
    static $INT = 'INT';
    static $BIGINT = 'BIGINT';
    static $TINYINT = 'TINYINT';
    static $SMALLINT = 'SMALLINT';
    static $MEDIUMINT = 'MEDIUMINT';
    static $FLOAT = 'FLOAT';
    static $DOUBLE = 'DOUBLE';
    static $LONG = 'LONG';

    private $type;
    private $name;
    private $length;
    private $unsigned;
    private $default;
    private $nullable;
    private $autoincrement;
    private $unique;
    private $comment;

    function __construct()
    {
        parent::__construct();
        $this->name = null;
        $this->type = null;
        $this->length = 0;
        $this->unsigned = false;
        $this->default = null;
        $this->nullable = false;
        $this->autoincrement = false;
        $this->unique = false;
        $this->comment = null;

        $paramaters = func_get_args();
        if(func_num_args() >= 2) {
            $this->name = $paramaters[0];
            $arr = $paramaters[1];
            if (is_array($arr)) {
                $this->type = strtoupper($arr["TYPE"]);
                if (isset($arr["SIZE"]) && ($arr["SIZE"] > 0)) {
                    $this->length = intval($arr["SIZE"]);
                }
                if (isset($arr["ATTRIBTE"])) {
                    if (preg_match('/UNSIGNED/i', $arr['ATTRIBTE'])) {
                        $this->unsigned = true;
                    }
                }
                if (isset($arr["NULL"]) && $arr["NULL"]) {
                    $this->nullable = true;
                }
                if (isset($arr["AUTO_INCREMENT"]) && $arr["AUTO_INCREMENT"]) {
                    $this->autoincrement = true;
                }
                if (array_key_exists("DEFAULT",$arr)) {
                    $this->default = $arr["DEFAULT"];
                }
                if (isset($arr["COMMENT"])) {
                    $this->comment = $arr["COMMENT"];
                }

                if (isset($arr["UNIQUE"]) && $arr["UNIQUE"] == true) {
                    $this->unique = true;
                }
            } else {
                $this->type = strtoupper($paramaters[1]);
                if (func_num_args() >= 3) {
                    if (isset($paramaters[2])) {
                        $this->length = $paramaters[2];
                    }
                }
            }
        }else if(func_num_args() >= 1) {
            $this->name = $paramaters[0];
        }
    }

    /**
     * @param $info
     * @return bool
     */
    public function setDBColumn($info)
    {
        $this->name = null;
        $this->type = null;
        $this->length = 0;
        $this->unsigned = false;
        $this->nullable = false;
        $this->autoincrement = false;
        $this->default = null;
        $this->unique = false;

        $success = false;
        foreach($info as $key => $val){
            $key = strtolower($key);
            if($key == 'field') {
                $this->name = $val;
                $success = true;
            }else if($key == 'type'){
                $type = $val;
                if(preg_match('/^(.+) (.+)$/',$val,$types)){
                    $type = $types[1];
                    if(preg_match('/unsigned/i',$types[2])){
                        $this->unsigned = true;
                    }
                }
                if(preg_match('/^(.+)\(([0-9]+)\)$/',$type,$mt)){
                    $this->type = strtoupper($mt[1]);
                    $this->length = intval($mt[2]);
                }else{
                    $this->type = strtoupper($type);
                }
            }else if($key == 'null'){
                if(strtolower($val) == 'yes'){
                    $this->nullable = true;
                }
            }else if($key == 'key'){
                if(preg_match('/PRI/i',$val)) {
                    //$this->autoincrement = true;
                }else if(preg_match('/UNI/i',$val)){
                    $this->unique = true;
                }
            }else if($key == 'default'){
                $this->default = $val;
            }else if($key == 'extra'){
                if(preg_match('/atuo_increment/',$val)){
                    $this->autoincrement = true;
                }
            }else if($key == 'comment'){
                $this->comment = $val;
            }
        }
        return $success;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setFieldType($type,$length=0)
    {
        $this->type = strtoupper($type);
        $this->length = $length;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getUnsigned()
    {
        return $this->unsigned;
    }

    /**
     * @param $unsigned
     */
    public function setUnsigned($unsigned)
    {
        $this->unsigned = $unsigned;
    }

    /**
     * @return boolean
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @param boolean $unique
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * @param mixed $nullable
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;
    }

    /**
     * @return mixed
     */
    public function getAutoincrement()
    {
        return $this->autoincrement;
    }

    /**
     * @param mixed $autoincrement
     */
    public function setAutoincrement($autoincrement)
    {
        $this->autoincrement = $autoincrement;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function infoSQL($create=false)
    {
        $attr = array();
        if($this->unsigned){
            $attr[] = 'UNSIGNED';
        }
        if($this->nullable){
            $attr[] = "NULL";
        }else{
            $attr[] = "NOT NULL";
        }
        if($this->autoincrement){
            $attr[] = "AUTO_INCREMENT";
        }
        if($create && $this->autoincrement){
            $attr[] = "PRIMARY KEY";
        }
        if($this->nullable && is_null($this->default)) {
            $attr[] = "DEFAULT NULL";
        }else if(!is_null($this->default)){
            $attr[] = "DEFAULT ".$this->default;
        }
        if($create && $this->unique){
            $attr[] = "UNIQUE";
        }
        if($this->comment){
            $attr[] = 'COMMENT '."'".$this->comment."'";
        }
        $sql = '`'.$this->name.'` ';
        if($this->length > 0){
            $sql .= $this->type.' ('.$this->length.') ';
        }else{
            $sql .= $this->type.' ';
        }
        $sql .= implode(" ",$attr);
        return $sql;
    }

    /**
     * @param string $action
     * @return string
     */
    public function alterSQL($action="ADD")
    {
        $sql = 'ALTER TABLE `'.$this->getTable()->getName().'` '.$action.' '.$this->infoSQL($action == 'ADD');
        return $sql;
    }

    /**
     * @return string
     */
    public function addPrimaryKeySQL()
    {
        return 'ALTER TABLE `'.$this->getTable()->getName().'` ADD PRIMARY KEY('.$this->getName().')';
    }

    /**
     * @return string
     */
    public function dropPrimaryKeySQL()
    {
        return 'ALTER TABLE `'.$this->getTable()->getName().'` DROP PRIMARY KEY';
    }

    /**
     * @return string
     */
    public function addUniqueSQL()
    {
        return 'ALTER TABLE `'.$this->getTable()->getName().'` ADD UNIQUE ('.$this->getName().')';
    }

    /**
     * @return string
     */
    public function dropUniqueSQL()
    {
        return 'DROP INDEX `'.$this->getName().'` ON `'.$this->getTable()->getName().'`';
    }

    /**
     * @param $val
     * @return mixed|string
     */
    public function rangeValue($val)
    {
        $type = strtoupper($this->type);
        switch($type){
            // 文字列変換
            case self::$CHAR:
            case self::$VARCHAR:
            case self::$TEXT:
                if($this->length > 0){
                    $val = mb_substr($val,0,$this->length);
                }
                break;
            case self::$DATE:
            case self::$DATETIME:
                if($val == ""){
                    $val = null;
                }
                break;
            // 数値変換
            case self::$TINYINT:
            case self::$SMALLINT:
            case self::$MEDIUMINT:
            case self::$INT:
            case self::$BIGINT:
            case self::$FLOAT:
            case self::$DOUBLE:
            case self::$LONG:
                $min = 0;
                $max = 0;
                if($type == self::$TINYINT) {
                    $min = -128;
                    $max = 127;
                }else if($type == self::$SMALLINT){
                    $min = -32768;
                    $max = 32767;
                }else if($type == self::$MEDIUMINT){
                    $min = -8388608;
                    $max = 8388607;
                }else if($type == self::$INT){
                    $min = -2147483648;
                    $max = 2147483647;
                }else if($type == self::$BIGINT){
                    $min = -9223372036854775808;
                    $max = 9223372036854775807;
                }
                if($min < 0 || $max > 0){
                    if($this->unsigned){
                        $max = ($max + ($min * -1));
                        $min = 0;
                    }
                    $val = max($val,$min);
                    $val = min($val,$max);
                }
                break;
        }
        return $val;
    }

    /**
     * @param $type
     * @return bool
     */
    static function isStringType($type)
    {
        $type = strtoupper($type);
        switch($type){
            // 文字列変換
            case self::$CHAR:
            case self::$VARCHAR:
            case self::$TEXT:
            case self::$DATE:
            case self::$DATETIME:
                return true;
                break;
            // 数値変換
            case self::$TINYINT:
            case self::$SMALLINT:
            case self::$MEDIUMINT:
            case self::$INT:
            case self::$BIGINT:
            case self::$FLOAT:
            case self::$DOUBLE:
            case self::$LONG:
                return false;
                break;
        }
        return false;
    }

    /**
     * DB用に値を変換する
     *
     * @param $str
     * @param null $default
     * @return string
     */
    static function toStringSQL($str,$default=NULL)
    {
        if(isset($str)){
            return "'".$str."'";
        }
        if(is_null($default) == false){
            return "'".$default."'";
        }
        return "NULL";
    }

    /**
     * DB用に値を変換する
     *
     * @param $str
     * @param null $default
     * @return int|string
     */
    static function toNumberSQL($str,$default=NULL)
    {
        if(isset($str)){
            if(is_numeric($str)){
                return $str;
            }
        }
        if(is_null($default) == false){
            return intval($default);
        }
        return "NULL";
    }


    /**
     * @param class_mysql_column $column
     * @return bool
     */
    public function isEnqueColumn(class_mysql_column $column)
    {
        if($this->name != $column->getName()) {
            return false;
        }else if($this->type != $column->getType()){
            return false;
        }else if($this->isStringType($this->type) && $this->length != $column->getLength()){
            return false;
        }else if($this->unique != $column->isUnique()){
            return false;
        }else if($this->default != $column->getDefault()){
            return false;
        }else if($this->nullable != $column->getNullable()){
            return false;
        }else if($this->autoincrement != $column->getAutoincrement()){
            return false;
        }else if($this->comment != $column->getComment()){
            return false;
        }
        return true;
    }
}

/**
 * Class class_mysql_index
 */
class class_mysql_index extends class_mysql_column_obj
{
    private $unique;
    private $name;
    private $columns;

    function __construct($name)
    {
        $this->unique = false;
        $this->name = null;
        $this->columns = array();
        if(is_array($name)){
            $this->setDBColumn($name);
        }else{
            $this->name = $name;
        }
    }

    /**
     * @return boolean
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @param boolean $unique
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param class_mysql_column $column
     */
    public function addColumn(class_mysql_column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @return string
     */
    public function alterSQL($action='ADD')
    {
        // ALTER TABLE版
        $sql = "ALTER TABLE `".$this->getTable()->getName()."` ".$action." ".$this->infoSQL();
        return $sql;
    }

    /**
     * @param $name
     * @param $columns
     * @return string
     * @throws Exception
     */
    static function alterMutableSQL($name,$columns)
    {
        $columns_sql = array();
        for($i=1;$i<count($columns);$i++){
            $column = $columns[$i];
            if($column instanceof class_mysql_index){
                $columns_sql[] = 'ADD '.$column->infoSQL();
            }else{
                throw new Exception('Not support params must be class_mysql_index');
            }
        }
        return "ALTER TABLE `".$name."` ".implode(",\n",$columns_sql)."\n";
    }

    /**
     * @return string
     */
    public function infoSQL()
    {
        $val = array();
        foreach($this->columns as $field_val){
            $val[] = "`".$field_val->getName()."`";
        }
        return "INDEX `".$this->name."`(".implode(",",$val).") ";
    }

    /**
     * @param $index
     */
    public function setDBColumn($index)
    {
        $this->unique = false;
        $this->name = null;
        $this->columns = array();
        foreach($index as $k => $v){
            $k = strtolower($k);
            if($k == 'key_name') {
                $this->name = $v;
            }else if($k == 'non_unique'){
                $this->unique = ($v != 0);
            }else if($k == 'column_name'){
                $this->addColumn(new class_mysql_column($v));
            }
        }
    }
}

/**
 * Class class_mysql_index
 */
class class_mysql_foreignkey extends class_mysql_column_obj
{
    static $ON_RESTRICT = 'RESTRICT';
    static $ON_CASCADE = 'CASCADE';
    static $ON_SETNULL = 'SET NULL';
    static $ON_NOACTION = 'NO ACTION';

    private $name;
    private $column;
    private $target;
    private $onupdate;
    private $ondelete;

    function __construct(class_mysql_column $column,class_mysql_column $target=null)
    {
        $this->name = null;
        $this->column = $column;
        $this->target = $target;
        $this->onupdate = null;
        $this->ondelete = null;
    }

    /**
     * @param class_mysql_column $target
     */
    public function setTarget(class_mysql_column $target)
    {
        $this->target = $target;
    }


    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getOnupdate()
    {
        return $this->onupdate;
    }

    /**
     * @param mixed $onupdate
     */
    public function setOnupdate($onupdate)
    {
        $this->onupdate = $onupdate;
    }

    /**
     * @return mixed
     */
    public function getOndelete()
    {
        return $this->ondelete;
    }

    /**
     * @param mixed $ondelete
     */
    public function setOndelete($ondelete)
    {
        $this->ondelete = $ondelete;
    }

    /**
     * @return string
     */
    public function alterSQL()
    {
        $sql = "ALTER TABLE `".$this->column->getTable()->getName()."` ADD ";
        if($this->name){
            $sql .= "CONSTRAINT ".$this->name." ";
        }
        $sql .= "FOREIGN KEY (`".$this->column->getName()."`) REFERENCES `".$this->target->getTable()->getName()."`(`".$this->target->getName()."`) ";
        if($this->ondelete){
            $sql .= "ON DELETE ".$this->ondelete." ";
        }
        if($this->onupdate){
            $sql .= "ON UPDATE ".$this->onupdate." ";
        }
        return $sql;
    }

    /**
     * @return string
     */
    public function dropSQL()
    {
        $sql = "ALTER TABLE `".$this->column->getTable()->getName()."` ";
        $sql .= "DROP FOREIGN KEY ".$this->name." ";

        return $sql;
    }
}

/**
 * Class class_mysql_table
 */
class class_mysql_table{
    static $INNODB = 'INNODB';
    static $MYISAM = 'MYISAM';

    private $name;
    private $engine;
    private $columns;
    private $indexs;
    private $foreignkeys;
    private $charaset;

    function __construct($name,$engine=null,$charaset=null)
    {
        $this->name = $name;
        $this->engine = $engine;
        $this->columns = array();
        $this->charaset = $charaset;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @return null
     */
    public function getCharaset()
    {
        return $this->charaset;
    }

    /**
     * @param class_mysql_column $column
     * @return $this
     */
    public function addColumn(class_mysql_column $column)
    {
        $column->setTable($this);
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @param $num
     * @return class_mysql_column
     */
    public function getColumn($num)
    {
        return $this->columns[$num];
    }

    /**
     * @return int
     */
    public function getColumnNum()
    {
        return count($this->columns);
    }

    /**
     * @param $name
     * @return class_mysql_column
     */
    public function findColumn($name)
    {
        $column = null;
        foreach($this->columns as $c){
            if($c->getName() == $name){
                $column = $c;
                break;
            }
        }
        return $column;
    }

    /**
     * @param class_mysql_index $column
     * @return $this
     */
    public function addIndex(class_mysql_index $index)
    {
        $index->setTable($this);
        $this->indexs[] = $index;
        return $this;
    }

    /**
     * @param $num
     * @return class_mysql_index
     */
    public function getIndex($num)
    {
        return $this->indexs[$num];
    }

    /**
     * @return int
     */
    public function getIndexNum()
    {
        return count($this->indexs);
    }

    /**
     * @param $name
     * @return class_mysql_index
     */
    public function findIndex($name)
    {
        $index = null;
        foreach($this->indexs as $c){
            if($c->getName() == $name){
                $index = $c;
                break;
            }
        }
        return $index;
    }

    /**
     * @param class_mysql_foreignkey $foreignkeys
     * @return $this
     */
    public function addForeignkey(class_mysql_foreignkey $foreignkeys)
    {
        $this->foreignkeys[] = $foreignkeys;
        return $this;
    }

    /**
     * @param $num
     * @return mixed
     */
    public function getForeignkeys($num)
    {
        return $this->foreignkeys[$num];
    }

    /**
     * @return int
     */
    public function getForeignkeysNum()
    {
        return count($this->foreignkeys);
    }

    /**
     * @param $name
     * @return class_mysql_foreignkey
     */
    public function findForeignkeys($name)
    {
        $index = null;
        foreach($this->foreignkeys as $c){
            if($c->getName() == $name){
                $index = $c;
                break;
            }
        }
        return $index;
    }

    /**
     * @param $ifexsist
     */
    public function createSQL($ifexist=false)
    {
        $sql = "CREATE TABLE ";
        if($ifexist){
            $sql .= 'IF NOT EXISTS ';
        }
        $sql .= '`'.$this->name.'` '."\n";
        if(count($this->columns) > 0){
            $columns = array();
            foreach($this->columns as $field_key => $field_val){
                $columns[] = $field_val->infoSQL(true);
            }
            $sql .= '('."\n".implode(",\n",$columns)."\n".')';

            if($this->engine){
                $sql .= ' ENGINE `'.$this->engine.'` ';
            }
            if($this->charaset){
                $sql .= 'CHARACTER SET `'.$this->charaset.'` ';
            }
        }
        return $sql;
    }

    /**
     * @return string
     */
    public function dropSQL()
    {
        return 'DROP TABLE `'.$this->name.'` ';
    }

    /**
     * @param $list
     */
    public function setDBColumns($columns)
    {
        $this->columns = array();
        foreach($columns as $column){
            $c = new class_mysql_column();
            if($c->setDBColumn($column)){
                $this->addColumn($c);
            }else{
                throw new Exception('un support DB Table Field.');
            }
        }
    }
    public function setDBIndexs($indexs)
    {
        $this->indexs = array();
        foreach($indexs as $index){
            $c = new class_mysql_index($index);
            $this->addIndex($c);
        }
    }
}

/**
 * Class class_mysql_connect
 */
class class_mysql_connect{
    var     $serverName  = 'localhost';
    var     $dbName      = '';
    var     $userName    = '';
    var     $passWord    = '';
    var     $linkId      = NULL;
    var     $charset     = 'utf8';
    var     $resResult   = NULL;
    var     $mysql_mode  = MYSQL_MODE_MYSQL;
    //====================================
    // 全般
    //====================================
    // 初期化
    function class_mysql_connect($server,$user,$password){
        $this->serverName = $server;
        $this->userName = $user;
        $this->passWord = $password;
        // mysqliチェック
        if(function_exists("mysqli_connect")){
            $this->mysql_mode = MYSQL_MODE_MYSQLI;
        }
    }

    /**
     * @param $message
     * @throws Exception
     */
    private function exception($message)
    {
        throw new Exception($message);
    }

    // DB設定
    function setDBName($name){
        $this->dbName = $name;
    }
    function setCharset($charset){
        $this->charset = $charset;
    }
    // DB作成
    function createDB($dbname){
        $success = false;
        // 接続
        $this->connectDB();
        // DB作成
        if($this->linkId){
            // for PHP4
            if($this->mysql_mode == MYSQL_MODE_MYSQL && function_exists('mysql_create_db')){
                if(mysql_create_db($dbname,$this->linkId)){
                    $success = true;
                }
            }else{
                $sql = 'CREATE DATABASE '.$dbname;
                if($this->mysql_mode == MYSQL_MODE_MYSQL){
                    if(mysql_query($sql, $this->linkId)){
                        $success = true;
                    }
                }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
                    if(mysqli_query($sql, $this->linkId)){
                        $success = true;
                    }
                }
            }
            $this->close();
        }
        return $success;
    }
    // DB削除
    function dropDB($dbname){
        $success = false;
        // 接続
        $this->connectDB();
        // DB削除
        if($this->linkId){
            // for PHP4
            if($this->mysql_mode == MYSQL_MODE_MYSQL && function_exists('mysql_drop_db')){
                if(mysql_drop_db($dbname,$this->linkId)){
                    $success = true;
                }
            }else{
                $sql = 'DROP DATABASE `'.$dbname."`";
                if($this->mysql_mode == MYSQL_MODE_MYSQL){
                    if(mysql_query($sql, $this->linkId)){
                        $success = true;
                    }
                }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
                    if(mysqli_query($sql, $this->linkId)){
                        $success = true;
                    }
                }
            }
            $this->close();
        }
        return $success;
    }
    //====================================
    // TABLE操作
    //====================================
    // SQL文を発行する(シンプル)
    function query_simple($q,$debug){
        $result = NULL;
        if($this->connect()){
            if((!$result = $this->query($q,false,$debug)) && $debug){
                print $this->error();
                exit;
            }
            $this->close();
        }
        return $result;
    }
    // DB接続
    function connectDB(){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            $this->linkId = mysql_connect($this->serverName,$this->userName,$this->passWord);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            $this->linkId = mysqli_connect($this->serverName,$this->userName,$this->passWord);
        }
        return $this->linkId;
    }
    function connect(){
        $this->connectDB();
        if($this->linkId){
//        mysql_client_encoding()
//        mysql_set_charset($this->charset);
//        mysql_real_escape_string
            if($this->mysql_mode == MYSQL_MODE_MYSQL && function_exists('mysql_set_charset')){
                if(mysql_set_charset($this->charset)){
                    $success = true;
                }
                mysql_select_db($this->dbName,$this->linkId);
            }else{
                if($this->mysql_mode == MYSQL_MODE_MYSQL){
                    mysql_query('SET NAMES '.$this->charset);
                    mysql_select_db($this->dbName,$this->linkId);
                }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
                    mysqli_set_charset($this->linkId,$this->charset);
                    mysqli_select_db($this->linkId,$this->dbName);
                }
            }
        }
        return $this->linkId;
    }
    // SQL文を発行する
    function sqlQuery($sql){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_query($sql, $this->linkId);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_query($this->linkId,$sql);
        }
        return null;
    }
    function query($sql,$auto,$debug){
        if($auto && !$this->linkId){
            $this->connect();
        }
        $this->resResult = $this->sqlQuery($sql);
        if($this->resResult){
            // レコード追加文だった場合
            if( preg_match( "/^INSERT/" , $sql ) ){
                return $this->lastId(false);
            }
            return true;
        }else{
            if($debug){
                print $this->error();
                print "<pre>";
                print_r(debug_backtrace());
                print "</pre>";
                exit;
            }
        }
        return $this->resResult;
    }
    // 最新の追加IDを取得
    function lastId($auto){
        $sql = 'SELECT LAST_INSERT_ID()';
        $result = $this->sqlQuery($sql);
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            $lastID = mysql_fetch_array($result);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            $lastID = mysqli_fetch_array($result);
        }
        return $lastID[0];
    }
    // 実行結果の行数を取得
    function numRows(){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_num_rows($this->resResult);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_num_rows($this->resResult);
        }
        return 0;
    }
    // 影響された行数を取得
    function affectedRows(){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_affected_rows($this->linkId);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_affected_rows($this->linkId);
        }
    }
    // 実行結果を配列・連想配列に格納する
    function fetchArray($result_type = MYSQL_ASSOC){
        if($this->numRows() > 0){
            if($this->mysql_mode == MYSQL_MODE_MYSQL){
                $type = MYSQL_BOTH;
                switch($result_type){
                    case MYSQLI_BOTH:       $type = MYSQL_BOTH; break;
                    case MYSQLI_ASSOC:      $type = MYSQL_ASSOC; break;
                    case MYSQLI_NUM:        $type = MYSQL_NUM; break;
                }
                return mysql_fetch_array( $this->resResult, $type );
            }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
                $type = MYSQLI_BOTH;
                switch($result_type){
                    case MYSQL_BOTH:       $type = MYSQLI_BOTH; break;
                    case MYSQL_ASSOC:      $type = MYSQLI_ASSOC; break;
                    case MYSQL_NUM:        $type = MYSQLI_NUM; break;
                }
                return mysqli_fetch_array( $this->resResult, $type );
            }
        }
        return NULL;
    }
    function fetchArrayAll(&$list,$result_type = MYSQL_ASSOC){
        if($this->numRows() > 0){
            $list = array();
            while($value = $this->fetchArray( $result_type )){
                $list[] = $value;
            }
        }
        return $this->numRows();
    }
    // 実行結果を最初に戻す
    function refresh(){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            mysql_data_seek($this->resResult, 0);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            mysqli_data_seek($this->resResult, 0);
        }
    }
    // 直前のエラーを返す
    function error(){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_error($this->linkId);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_error($this->linkId);
        }
        return null;
    }
    // DB切断
    function close(){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            mysql_close($this->linkId);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            mysqli_close($this->linkId);
        }
        $this->linkId = NULL;
    }
    // フィールド数を取得
    function fieldsCount(){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_num_fields($this->linkId);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_field_count($this->linkId);
        }
        return null;
    }
    // フィールド情報を取得
    function fieldName($result,$num){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_field_name($result,$num);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_fetch_field_direct($result,$num);
        }
        return null;
    }
    function fieldType($result,$num){
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_field_type($result,$num);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_fetch_field_direct($result,$num);
        }
        return null;
    }
    // テーブル情報を取得する
    function dbInfo(){
//  if($this->linkId){
        $query = "SHOW TABLE STATUS";
//        mysql_query("set names ".$this->charset,$this->linkId);
        $result = $this->sqlQuery($query);
        if($result){
            $value_list = array();
            while($value = $this->fetchArray(MYSQL_ASSOC)){
                $value_list[] = $value;
            }
            return $value_list;
        }
//  }
        return null;
    }
    // テーブル情報を取得する
    function tableInfo($table_name){
        // SQL文作成
        $sql = "SHOW COLUMNS FROM `".$table_name."`";
        // SQL発行
        if($this->query($sql,true,true)){
            $valuelist = array();
            while($list = $this->fetchArray()){
                $valuelist[] = $list;
            }
            return $valuelist;
        }
        return null;
    }
    // データベースをエクスポートする
    function exportCSV($table_name,$option=null){
        // オプションの取得
        $enclosed = "'";
        $terminated = ",";
        if(isset($option)){
            if(isset($option['enclosed'])){
                $enclosed = $option['enclosed'];
            }
            if(isset($option['terminated'])){
                $terminated = $option['terminated'];
            }
        }
        $str = '';
        $query = 'SELECT * from `'.$table_name."`";
        // SQL文発行
//  mysql_query("set names ".$this->charset,$this->linkId);
        if( $result = $this->sqlQuery($query) ){
            // フィールド数の取得
            $fields_count = $this->fieldsCount($result);
            if($fields_count > 0){
                $fields = array();
                // レコード名取得
                for($i=0;$i<$fields_count;$i++){
                    $data = array();
                    // フィールド名取得
                    $data['name'] = $this->fieldName($result,$i);
                    $data['type'] = $this->fieldType($result,$i);
                    // データ追加
                    $fields[] =$data;
                }
                // フィールド出力
                foreach($fields as $key => $val){
                    if($key != 0){
                        $str .= $terminated;
                    }
                    $str .= $val['name'];
                    $str .= $val['type'];
                }
                $str .= "\n";
                // レコード出力
                while( $list = $this->fetchArray( MYSQL_ASSOC ) ){
                    foreach($fields as $key => $val){
                        if($key != 0){
                            $str .= $terminated;
                        }
                        $data = $list[$val['name']];
                        $string_array = array('string','blob');
                        // 文字列なら区切り文字を出力
                        if(in_array($val['type'],$string_array)){
                            $data = $enclosed.$data.$enclosed;
                        }
                        $str .= $data;
                    }
                    $str .= "\n";
                }
            }
        }
        return $str;
    }

    /**
     * @param $dbname
     * @param array $fields
     * @param string $charaset
     * @param string $engine
     * @return array
     */
    public function migrationTableSQL($dbname,$fields=array(),$charaset="",$engine="InnoDB")
    {
        $sqls = array();
        // 現在のテーブル情報を取得
        $sql = 'SHOW FULL COLUMNS FROM `'.$dbname.'`;';
        // SQL
        if($this->query($sql,true,false) && $this->numRows() > 0){
            $list = array();
            while($value = $this->fetchArray()){
                $list[] = $value;
            }
            // 現在のテーブル
            $current_table = new class_mysql_table($dbname,$engine,$charaset);
            $current_table->setDBColumns($list);

            // 現在のテーブル情報を取得
            $sql = 'SHOW INDEX FROM `'.$dbname.'`;';
            // SQL
            if($this->query($sql,true,false) && $this->numRows() > 0){
                while($value = $this->fetchArray()){
                    if($c = $current_table->findColumn($value['Column_name'])){
                        if($value['Key_name'] == 'PRIMARY') {
                            $c->setAutoincrement(true);
                        }else if($value['Non_unique'] == 0){
                            $c->setUnique(true);
                        }
                    }
                }
            }

            // 新しいテーブル
            $table = new class_mysql_table($dbname,$engine,$charaset);
            foreach($fields as $key => $field){
                $table->addColumn(new class_mysql_column($key,$field));
            }

            for($i=0;$i<$table->getColumnNum();$i++){
                $column = $table->getColumn($i);
                if($c = $current_table->findColumn($column->getName())){
                    if(!$column->isEnqueColumn($c)){
                        if($column->getAutoincrement() != $c->getAutoincrement()){
                            if($column->getAutoincrement()) {
                                $sqls[] = $column->addPrimaryKeySQL();
                            }else{
                                $sqls[] = $column->dropPrimaryKeySQL();
                            }
                        }
                        if($column->isUnique() != $c->isUnique()){
                            if($column->isUnique()) {
                                $sqls[] = $column->addUniqueSQL();
                            }else{
                                $sqls[] = $column->dropUniqueSQL();
                            }
                        }
                        $sqls[] = $column->alterSQL('MODIFY');
                    }
                }else{
                    $sqls[] = $column->alterSQL();
                }
            }
        }else{
            $sqls[] = $this->createTableSQL($dbname,$fields,$charaset,$engine);
        }
        return $sqls;
    }

    /**
     * テーブル作成SQL文を作成
     *
     * @param $dbname
     * @param array $fields
     * @param string $charaset
     * @param string $engine
     * @return string
     */
    public function createTableSQL($dbname,$fields=array(),$charaset="",$engine="InnoDB")
    {
        $table = new class_mysql_table($dbname,$engine,$charaset);
        foreach($fields as $key => $field){
            if(is_array($field)){
                $table->addColumn(new class_mysql_column($key,$field));
            }else{
                $table->addColumn($field);
            }
        }
        return $table->createSQL();
    }

    /**
     * References
     *
     * @param $dbname
     * @param $key_name
     * @param $field
     * @return string
     */
    public function createReferencesSQL($dbname,$key_name,$field){
        list($tn,$tv) = explode('.',$field['TARGET']);

        $column = new class_mysql_column($key_name);
        $column->setTable(new class_mysql_table($dbname));
        $target = new class_mysql_column($tv);
        $target->setTable(new class_mysql_table($tn));
        $references = new class_mysql_foreignkey($column,$target);
        if(isset($field['CONSTRAINT'])){
            $references->setName($field['CONSTRAINT']);
        }
        if(isset($field['DELETE'])){
            $references->setOndelete($field['DELETE']);
        }
        if(isset($field['UPDATE'])){
            $references->setOnupdate($field['UPDATE']);
        }
        return $references->alterSQL();
    }

    /**
     * @param $dbname
     * @param $key_name
     * @return string
     */
    public function deleteReferencesSQL($dbname,$key_name){
        $column = new class_mysql_column($key_name);
        $column->setTable(new class_mysql_table($dbname));
        $references = new class_mysql_foreignkey($column);

        return $references->dropSQL();
    }

    /**
     * @param $dbname
     * @param array $fields
     * @param string $charaset
     * @param string $engine
     * @return array
     */
    public function migrationReferencesSQL($dbname,$fields=array())
    {
        $sqls = array();
        // TODO : migration References Key
        return $sqls;
    }

    /**
     * @param $dbname
     * @param array $fields
     * @param string $charaset
     * @param string $engine
     * @return array
     */
    public function migrationIndexSQL($dbname,$fields=array())
    {
        $sqls = array();
        // 現在のテーブル情報を取得
        $sql = 'SHOW INDEX FROM `'.$dbname.'`;';
        // SQL
        $this->query($sql,true,false);
        if($this->numRows() > 0){
            $list = array();
            while($value = $this->fetchArray()){
                $list[] = $value;
            }

            // 現在のテーブル
            $current_table = new class_mysql_table($dbname);
            $current_table->setDBIndexs($list);
            // 新しいテーブル
            $table = new class_mysql_table($dbname);

            $index_columns = array();
            foreach($fields as $field_key => $field_val){
                $index_column = new class_mysql_index($field_key);
                foreach($field_val as $key => $v){
                    $index_column->addColumn(new class_mysql_column($v));
                }
                $table->addIndex($index_column);
            }

            for($i=0;$i<$table->getIndexNum();$i++){
                $index = $table->getIndex($i);
                if($c = $current_table->findIndex($index->getName())){
                }else{
                    $sqls[] = $index->alterSQL();
                }
            }
        }else{
            $sqls[] = $this->createIndexSQL($dbname,$fields);
        }
        return $sqls;
    }

    /**
     * INDEX作成
     *
     * @param $dbname
     * @param array $fields
     * @return string
     */
    public function createIndexSQL($dbname,$fields=array())
    {
        $index_columns = array();
        foreach($fields as $field_key => $field_val){
            $index_column = new class_mysql_index($field_key);
            foreach($field_val as $key => $v){
                $index_column->addColumn(new class_mysql_column($v));
            }
            $index_columns[] = $index_column;
        }
        return class_mysql_index::alterMutableSQL($dbname,$index_columns);
    }

    /**
     * COLUMN作成
     *
     * @param $name
     * @param array $fields
     * @return string
     */
    public function createColumnSQL($name,$fields=array()){
        $column = new class_mysql_column($name,$fields);
        return $column->infoSQL(true);
    }

    /**
     * テーブル削除SQL文を作成
     *
     * @param $dbname
     * @return string
     */
    public function deleteTableSQL($dbname){
        $table = new class_mysql_table($dbname);
        return $table->dropSQL();
    }

    /**
     * カラム追加
     *
     * @param $dbname
     * @param $name
     * @param array $column
     * @param string $after
     * @return string
     */
    public function alterTableSQL($dbname,$name,$column=array(),$after=""){
        $column = new class_mysql_column($name,$column);
        $column->setTable(new class_mysql_table($dbname));

        return $column->alterSQL().(($after != '') ? ' '.$after : '');
    }
    //----------------------------------------
    // SQL作成
    //----------------------------------------
    // レコード追加SQL文を作成
    function insertRecodeSQL($dbname,$args){
        $sql = 'INSERT INTO `'.$dbname.'` (';
        $count = 0;
        $args_count = count($args);
        foreach($args as $key => $value){
            $sql .= '`'.$key.'` ';
            $count ++;
            if($count < $args_count){
                $sql .= ', ';
            }
        }
        $sql .= ') ';
        $sql .= 'VALUES (';
        $count = 0;
        foreach($args as $key => $value){
            $sql .= $value;
            $count ++;
            if($count < $args_count){
                $sql .= ', ';
            }
        }
        $sql .= ') ';
        return $sql;
    }
    // レコード更新SQL文を作成
    function updateRecodeSQL($dbname,$args,$where){
        $count = 0;
        $args_count = count($args);

        $sql = 'UPDATE `'.$dbname.'` SET ';
        foreach($args as $key => $value){
            $sql .= '`'.$key.'` = '.$value.' ';
            $count ++;
            if($count < $args_count){
                $sql .= ', ';
            }
        }
        $sql .= $where;
        return $sql;
    }
    // レコード削除SQL文を作成
    function deleteRecodeSQL($dbname,$where){
        $sql = 'DELETE FROM `'.$dbname.'` ';
        $sql .= $where;
        return $sql;
    }
    /*// SELECT文を発行する
    function selectSqlEx($select_keys,$db_name,$joins,$where,){
          $sql = "SELECT ";
          if(is_array($select_keys)){
              $sql .= implode(",",$select_keys)." ";
          }else{
              $sql .= "`".$select_keys."` ";
          }
          $sql .= " FROM ".$db_name." ";
          if(isset($joins)){
              foreach($joins as $val){
                      if($joins["TYPE"] == "LEFT"){
                              $sql .= "LEFT JOIN ";
                      }elseif($joins["TYPE"] == "RIGHT"){
                              $sql .= "RIGHT JOIN ";
                      }
                      $sql .= "`".$val["NAME"]."` ON ".$val["WHERE"]." "
              }
          }
          if(is_array($where)){
              $sql .= "WHERE ".implode(" ",$select_keys)." ";
          }
          return $sql;
    }*/
    //----------------------------------------
    // データ変換
    //----------------------------------------
    // エスケープ
    function escapeSQL($str){
        if(!$this->linkId){
            $this->connect();
        }
        if($this->mysql_mode == MYSQL_MODE_MYSQL){
            return mysql_real_escape_string($str);
        }else if($this->mysql_mode == MYSQL_MODE_MYSQLI){
            return mysqli_real_escape_string($this->linkId,$str);
        }
        //return addslashes($str);
    }

    /**
     * DB用に値を変換する
     *
     * @param $str
     * @param null $default
     * @return string
     */
    public function toStringSQL($str,$default=NULL){
        if(isset($str)){
            $str = $this->escapeSQL($str);
            return class_mysql_column::toStringSQL($str,$default);
        }
        return class_mysql_column::toStringSQL($str,$default);
    }

    /**
     * DB用に値を変換する
     *
     * @param $str
     * @param null $default
     * @return int|string
     */
    public function toNumberSQL($str,$default=NULL){
        return class_mysql_column::toNumberSQL($str,$default);
    }

    /**
     * @param $val
     * @param $type
     * @param null $default
     * @param int $length
     * @param bool $unsigned
     * @return int|string
     */
    public function toSqlValueSQL($val,$type,$default=NULL,$length=0,$unsigned=false)
    {
        $column = new class_mysql_column('');
        $column->setFieldType($type,$length);
        $column->setUnsigned($unsigned);
        $column->setDefault($default);

        $val = $column->rangeValue($val);

        if(class_mysql_column::isStringType($column->getType())){
            return $this->toStringSQL($val,$column->getDefault());
        }
        return $this->toNumberSQL($val,$column->getDefault());
    }

    /**
     * @param $valuelist
     * @param $tablevalue
     * @return mixed
     */
    public function toSqlValueListSQL($valuelist,$tablevalue){
        foreach($valuelist as $key => $val){
            //if(in_array($tkey,$valuelist)){
            $tv = $tablevalue[$key];
            if(isset($tv)){
                $length = isset($tv["SIZE"]) ? intval($tv["SIZE"]) : 0;
                $unsigned = (isset($tv["ATTRIBTE"]) && preg_match('/UNSIGNED/i',$tv['ATTRIBTE'])) ? true : false;
                if(isset($tv["DEFAULT"])){
                    $valuelist[$key] = $this->toSqlValueSQL($val,$tv["TYPE"],$tv["DEFAULT"],$length,$unsigned);
                }else{
                    $valuelist[$key] = $this->toSqlValueSQL($val,$tv["TYPE"],null,$length,$unsigned);
                }
            }
        }
        return $valuelist;
    }
}

/**
 * マルチDBクラス
 *
 * Class class_mysql
 */
class class_mysql{
    var     $db;
    var     $current;
    var     $auto_connect = true;
    var     $debug = false;

    /**
     * コンストラクタ
     */
    function class_mysql(){
        $this->db = array();
        $this->current = 0;
    }

    /**
     * DB用配列文字列変換クラス
     *
     * @param $ary
     * @param $sep
     * @return string
     */
    function arrayToSqlString($ary,$sep){
        $str = "";
        if(is_array($ary)){
            $i = 0;
            foreach($ary as $c){
                if(is_numeric($c)){
                    $str .= $sep.$c;
                }
                $i ++;
            }
        }
        $str .= $sep;
        return $str;
    }

    /**
     * @param $str
     * @param $sep
     * @return array
     */
    function sqlStringToArray($str,$sep){
        $ary = preg_split("/".preg_quote($sep)."/",$str);
        // 配列の最初と最後を取り除く
        array_shift($ary);
        array_pop($ary);
        return $ary;
    }

    /**
     * @param $format
     * @param $date
     * @return bool|string
     */
    function dateFormat($format,$date){
        return date($format,$this->dateToTimestamp($date));
    }

    /**
     * DATETIMEまたはDATEをタイムスタンプに変換
     *
     * @param $date
     * @return int
     */
    function dateToTimestamp($date){
        return strtotime($date);
    }

    /**
     * タイムスタンプをmysqlのDatetimeに変換
     * @param null $timestamp
     * @return bool|null|string
     */
    function timestampToDatetime($timestamp){
        if(is_null($timestamp)){
            return null;
        }
        return date("Y-m-d H:i:s",$timestamp);
    }

    /**
     * @param null $timestamp
     * @return bool|string
     */
    function timestampToDate($timestamp){
        if(is_null($timestamp)){
            return null;
        }
        return date("Y-m-d",$timestamp);
    }

    //====================================
    // DBサーバー追加
    //====================================
    // DB追加
    function addDB($server,$user,$password){
        $db = new class_mysql_connect($server,$user,$password);
        $this->db[] = $db;
    }
    // DB選択
    function setCurrentDB($num){
        $this->current = $num;
    }
    function setAutoConnect($flg){
        $this->auto_connect = $flg;
    }
    function setDebug($flg){
        $this->debug = $flg;
    }
    function get($id=-1){
        if($id < 0){
            $id = $this->current;
        }
        return $this->db[$id];
    }
    // DB設定
    function setDBName($name){
        return $this->db[$this->current]->setDBName($name);
    }
    // DB作成
    function createDB($dbname){
        return $this->db[$this->current]->createDB($dbname);
    }
    function dropDB($dbname){
        return $this->db[$this->current]->dropDB($dbname);
    }
    //====================================
    // TABLE操作
    //====================================
    // SQL文を発行する(シンプル)
    function query_simple($q){
        return $this->db[$this->current]->query_simple($q,$this->debug);
    }
    // DB接続
    function connect(){
        return $this->db[$this->current]->connect();
    }
    // SQL文を発行する
    function query($q){
        return $this->db[$this->current]->query($q,$this->auto_connect,$this->debug);
    }

    /**
     *  SQL実行(パラメータ部分が置換)
     *
     * @param $q
     * @param array $params
     * @return mixed
     */
    function exec($q,$params = array()){
        return $this->query($this->execFormat($q,$params));
    }
    function execFormat($q,$params = array()){
        foreach($params as $key => $val){
            $q = str_replace(':'.$key.':',$val,$q);
            $q = str_replace(':%'.$key.'%:',$this->escapeSQL($val),$q);
        }
        return $q;
    }
    // 最新の追加IDを取得
    function lastId(){
        return $this->db[$this->current]->lastId($this->auto_connect);
    }
    // 実行結果の行数を取得
    function numRows(){
        return $this->db[$this->current]->numRows();
    }
    // 影響された行数を取得
    function affectedRows(){
        return $this->db[$this->current]->affectedRows();
    }
    // 実行結果を配列・連想配列に格納する
    function fetchArray($result_type = MYSQL_ASSOC){
        return $this->db[$this->current]->fetchArray($result_type);
    }
    function fetchArrayAll(&$list,$result_type = MYSQL_ASSOC){
        return $this->db[$this->current]->fetchArrayAll($list,$result_type);
    }
    function fetchArrayResult($prex=null){
        $result = $this->fetchArray(MYSQL_ASSOC);
        if(!$result){
            return null;
        }
        if($prex){
            $result = $this->replacePrex($result,$prex,'');
        }
        return $result;
    }
    function fetchArrayResults($prex=null){
        $valuelist = array();
        while($value = $this->fetchArrayResult($prex)){
            $valuelist[] = $value;
        }
        return $valuelist;
    }
    function findOneColum($default=null){
        if($this->numRows() > 0){
            $res = $this->fetchArray(MYSQL_BOTH);
            if($res && isset($res[0])){
                return $res[0];
            }
        }
        return $default;
    }
    function foundRows(){
        $sql = 'SELECT FOUND_ROWS() as `all`';
        $this->query($sql);
        if($this->numRows() > 0){
            return $this->findOneColum(0);
        }
        return 0;
    }
    // プレックスの追加
    function addPrex($value,$prex){
        if($value){
            $postvalue = array();
            foreach($value as $k => $v){
                $postvalue[$prex.$k] = $v;
            }
            return $postvalue;
        }
        return $value;
    }
    function addPrexArray($valuelist,$prex){
        foreach($valuelist as $key => $value){
            $valuelist[$key] = $this->addPrex($value,$prex);
        }
        return $valuelist;
    }
    // プレックスの変更
    function replacePrex($value,$prex,$rename_prex){
        if($value){
            $result = array();
            foreach($value as $key => $val){
                if($prex && preg_match('/^'.preg_quote($prex,'/').'(.+)$/',$key,$matchs)){
                    $result[$rename_prex.$matchs[1]] = $val;
                }else{
                    $result[$key] = $val;
                }
            }
            return $result;
        }
        return $value;
    }
    function replacePrexArray($valuelist,$prex,$rename_prex){
        foreach($valuelist as $key => $value){
            $valuelist[$key] = $this->replacePrex($value,$prex,$rename_prex);
        }
        return $valuelist;
    }
    // 実行結果を最初に戻す
    function refresh(){
        return $this->db[$this->current]->refresh();
    }
    // 直前のエラーを返す
    function error(){
        return $this->db[$this->current]->error();
    }
    // DB切断
    function close(){
        return $this->db[$this->current]->close();
    }
    // テーブル情報を取得する
    function dbInfo(){
        return $this->db[$this->current]->dbInfo();
    }
    // テーブル情報を取得する
    function tableInfo($table_name){
        return $this->db[$this->current]->tableInfo($table_name);
    }
    // データベースをエクスポートする
    function exportCSV($table_name,$option=null){
        return $this->db[$this->current]->exportCSV($table_name,$option);
    }

    //----------------------------------------
    // テープル作成・削除
    //----------------------------------------
    // テーブル作成SQL文を作成
    function createTableSQL($dbname,$fields=array(),$charaset="",$engine="InnoDB"){
        return $this->db[$this->current]->createTableSQL($dbname,$fields,$charaset,$engine);
    }
    // INDEX作成
    function createIndexSQL($dbname,$fields=array()){
        return $this->db[$this->current]->createIndexSQL($dbname,$fields);
    }
    // References作成
    function createReferencesSQL($dbname,$key_name,$fields){
        return $this->db[$this->current]->createReferencesSQL($dbname,$key_name,$fields);
    }
    function deleteReferencesSQL($dbname,$key_name){
        return $this->db[$this->current]->deleteReferencesSQL($dbname,$key_name);
    }
    // COLUMN作成
    function createColumnSQL($dbname,$fields=array()){
        return $this->db[$this->current]->createColumnSQL($dbname,$fields);
    }
    // テーブル削除SQL文を作成
    function deleteTableSQL($dbname){
        return $this->db[$this->current]->deleteTableSQL($dbname);
    }
    // カラム追加
    function alterTableSQL($dbname,$name,$column=array(),$after=""){
        return $this->db[$this->current]->alterTableSQL($dbname,$name,$column,$after);
    }
    // マイグレーション
    function migrationTableSQL($dbname,$fields=array(),$charaset="",$engine="InnoDB"){
        return $this->db[$this->current]->migrationTableSQL($dbname,$fields,$charaset,$engine);
    }
    // マイグレーション
    function migrationIndexSQL($dbname,$fields=array()){
        return $this->db[$this->current]->migrationIndexSQL($dbname,$fields);
    }
    // マイグレーション
    function migrationReferencesSQL($dbname,$key_name,$fields){
        return $this->db[$this->current]->migrationReferencesSQL($dbname,$key_name,$fields);
    }
    //----------------------------------------
    // SQL作成
    //----------------------------------------
    // レコード追加SQL文を作成
    function insertRecodeSQL($dbname,$args){
        return $this->db[$this->current]->insertRecodeSQL($dbname,$args);
    }
    // レコード更新SQL文を作成
    function updateRecodeSQL($dbname,$args,$where){
        return $this->db[$this->current]->updateRecodeSQL($dbname,$args,$where);
    }
    // レコード削除SQL文を作成
    function deleteRecodeSQL($dbname,$where){
        return $this->db[$this->current]->deleteRecodeSQL($dbname,$where);
    }
    //----------------------------------------
    // データ変換
    //----------------------------------------
    // エスケープ
    function escapeSQL($str){
        return $this->db[$this->current]->escapeSQL($str);
    }
    function toSqlValueListSQL($valuelist,$tablevalue){
        return $this->db[$this->current]->toSqlValueListSQL($valuelist,$tablevalue);
    }
}
