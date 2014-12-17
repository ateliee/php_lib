<?php
//============================================
// class_mysql.php
//============================================

define("MYSQL_MODE_MYSQL",1);
define("MYSQL_MODE_MYSQLI",2);
define("MYSQL_MODE_PDO",3);
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
    //----------------------------------------
    // テープル作成・削除
    //----------------------------------------
    // テーブル作成SQL文を作成
    function createTableSQL($dbname,$fields=array(),$charaset="",$engine="InnoDB"){
        $sql = "CREATE TABLE `".$dbname."` ";
        if(is_array($fields) && count($fields) > 0){
            $column = array();
            foreach($fields as $field_key => $field_val){
                $s = $this->createColumnSQL($field_key,$field_val);
                if($s != ""){
                    $column[] = $s."\n";
                }
            }
            $fields_sql = '';
            $fields_sql .= "("."\n";
            $fields_sql .= implode(",",$column);
            // インデックス
            if(isset($fields["INDEX"])){
                $p = array();
                foreach($fields["INDEX"] as $index_name => $index_value){
                    $p[] = "INDEX ".$index_name." ( '".$index_value."' )\n";
                }
                $fields_sql .= implode(',',$p);
            }
            /*
            // プライマリキー
            if($primary_key != ""){
                  $fields_sql .= "PRIMARY KEY ( '".$primary_key."' )\n";
            }
            */
            $fields_sql .= ") ";

            $sql .= $fields_sql;
            if($engine != ""){
                $sql .= 'ENGINE `'.$engine.'` ';
            }
            if($charaset != ""){
                $sql .= 'CHARACTER SET `'.$charaset.'` ';
            }
        }
        return $sql;
    }
    // References
    function createReferencesSQL($dbname,$key_name,$field){
        list($tn,$tv) = explode('.',$field['TARGET']);
        $sql = "ALTER TABLE `".$dbname."` ADD ";
        if(isset($field['CONSTRAINT'])){
            $sql .= "CONSTRAINT ".$field['CONSTRAINT']." ";
        }
        $sql .= "FOREIGN KEY (`".$key_name."`) REFERENCES `".$tn."`(`".$tv."`) ";
        if(isset($field['DELETE'])){
            $sql .= "ON DELETE ".$field['DELETE']." ";
        }
        if(isset($field['UPDATE'])){
            $sql .= "ON UPDATE ".$field['UPDATE']." ";
        }

        return $sql;
    }
    function deleteReferencesSQL($dbname,$key_name){
        $sql = "ALTER TABLE `".$dbname."` ";
        $sql .= "DROP FOREIGN KEY ".$key_name." ";

        return $sql;
    }
    // INDEX作成
    function createIndexSQL($dbname,$fields=array()){
        /*// CREATE INDEX版
        $sql = "";
        if(is_array($fields) && count($fields) > 0){
              foreach($fields as $field_key => $field_val){
                    $val = array();
                    foreach($field_val as $key => $v){
                            $val[] = "`".$v."`";
                    }
                    $sql .= "CREATE INDEX `".$field_key."` ON `".$dbname."`(".implode(",",$val)."); \n";
              }
        }*/
        // ALTER TABLE版
        $sql = "ALTER TABLE `".$dbname."` ";
        if(is_array($fields) && count($fields) > 0){
            $i = 0;
            $fields_count = count($fields);
            foreach($fields as $field_key => $field_val){
                $val = array();
                foreach($field_val as $key => $v){
                    $val[] = "`".$v."`";
                }
                $sql .= "ADD INDEX `".$field_key."`(".implode(",",$val).") ";
                $i ++;
                if($i < $fields_count){
                    $sql .= ",\n";
                }else{
                    $sql .= "\n";
                }
            }
        }
        return $sql;

        /*
              $sql = "CREATE INDEX ".$dbname." ";
              if(is_array($fields) && count($fields) > 0){
                    $i = 0;
                    $fields_count = count($fields);

                    $fields_sql = '';
                    $fields_sql .= "("."\n";
                    foreach($fields as $field_key => $field_val){
                          if(is_array($field_val)){
                                if(is_int($field_val["SIZE"]) && $field_val["SIZE"] > 0){
                                      $type = $field_val["TYPE"]."(".$field_val["SIZE"].")";
                                }else{
                                      $type = $field_val["TYPE"];
                                }
                                $attr = array();
                                if(isset($field_val["NULL"]) && $field_val["NULL"]){
                                      $attr[] = "NULL";
                                }else{
                                      $attr[] = "NOT NULL";
                                }
                                if(isset($field_val["AUTO_INCREMENT"]) && $field_val["AUTO_INCREMENT"]){
                                      $attr[] = "AUTO_INCREMENT PRIMARY KEY";
                                }
                                if(isset($field_val["DEFAULT"]) && $field_val["DEFAULT"] != ""){
                                      $attr[] = "DEFAULT ".$field_val["DEFAULT"];
                                }
                                if(isset($field_val["UNIQUE"]) && $field_val["UNIQUE"] == true){
                                      $attr[] = "UNIQUE";
                                }
                                $fields_sql .= "`".$field_key."` ".$type." ".implode(" ",$attr);

                                $i ++;
                                // if($i < $fields_count || $primary_key != ""){
                                if($i < $fields_count){
                                      $fields_sql .= ",\n";
                                }else{
                                      $fields_sql .= "\n";
                                }
                          }
                    }
              }
              */
    }
    // COLUMN作成
    function createColumnSQL($name,$fields=array()){
        if(is_int($fields["SIZE"]) && $fields["SIZE"] > 0){
            $type = $fields["TYPE"]."(".$fields["SIZE"].")";
        }else{
            $type = $fields["TYPE"];
        }
        $attr = array();
        if(isset($fields["ATTRIBTE"]) && $fields["ATTRIBTE"] != ""){
            $attr[] = $fields["ATTRIBTE"];
        }
        if(isset($fields["NULL"]) && $fields["NULL"]){
            $attr[] = "NULL";
        }else{
            $attr[] = "NOT NULL";
        }
        if(isset($fields["AUTO_INCREMENT"]) && $fields["AUTO_INCREMENT"]){
            $attr[] = "AUTO_INCREMENT PRIMARY KEY";
        }
        if(isset($fields["DEFAULT"])){
            $attr[] = "DEFAULT ".(is_null($fields["DEFAULT"]) ? "NULL" : $fields["DEFAULT"]);
        }
        if(isset($fields["UNIQUE"]) && $fields["UNIQUE"] == true){
            $attr[] = "UNIQUE";
        }
        $sql = "`".$name."` ".$type." ".implode(" ",$attr);
        return $sql;
    }
    // テーブル削除SQL文を作成
    function deleteTableSQL($dbname){
        $sql = 'DROP TABLE `'.$dbname.'` ';
        return $sql;
    }
    // カラム追加
    function alterTableSQL($dbname,$name,$column=array(),$after=""){
        $sql = 'ALTER TABLE `'.$dbname.'` ADD '.$this->createColumnSQL($name,$column)." ".$after;
        return $sql;
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
    // DB用に値を変換する
    function toStringSQL($str,$default=NULL){
        if(isset($str)){
            //if($html){
            //      $str = htmlspecialchars($str);
            //}
            //$str = addslashes($str);
            $str = $this->escapeSQL($str);
            return "'".$str."'";
        }
        if(is_null($default) == false){
            return "'".$default."'";
        }
        return "NULL";
    }
    function toNumberSQL($str,$default=NULL){
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
     * @param $val
     * @param $type
     * @param null $default
     * @param int $length
     * @return int|string
     */
    function toSqlValueSQL($val,$type,$default=NULL,$length=0){
        $type = strtoupper($type);
        switch($type){
            // 文字列変換
            case 'CHAR':
            case 'VARCHAR':
            case 'TEXT':
                if($length > 0){
                    $val = mb_substr($val,0,$length);
                }
                $val = $this->toStringSQL($val,$default);
                break;
            case 'DATE':
            case 'DATETIME':
                if(is_null($default)){
                    if($val == "" || is_null($val)){
                        return "NULL";
                    }
                }
                $val = $this->toStringSQL($val,$default);
                break;
            // 数値変換
            case 'INT':
            case 'BIGINT':
            case 'FLOAT':
            case 'DOUBLE':
            case 'LONG':
                /*if(isset($default) == false){
                      $default = "0";
                }*/
                $val = $this->toNumberSQL($val,$default);
                break;
        }
        return $val;
    }

    /**
     * @param $valuelist
     * @param $tablevalue
     * @return mixed
     */
    function toSqlValueListSQL($valuelist,$tablevalue){
        foreach($valuelist as $key => $val){
            //if(in_array($tkey,$valuelist)){
            $tv = $tablevalue[$key];
            if(isset($tv)){
                $length = isset($tv["SIZE"]) ? intval($tv["SIZE"]) : 0;
                if(isset($tv["DEFAULT"])){
                    $valuelist[$key] = $this->toSqlValueSQL($val,$tv["TYPE"],$tv["DEFAULT"],$length);
                }else{
                    $valuelist[$key] = $this->toSqlValueSQL($val,$tv["TYPE"],null,$length);
                }
            }
        }
        return $valuelist;
    }
}
// マルチDBクラス
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
        foreach($params as $key => $val){
            $q = str_replace(':'.$key.':',$val,$q);
            $q = str_replace(':%'.$key.'%:',$this->escapeSQL($val),$q);
        }
        return $this->query($q);
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
