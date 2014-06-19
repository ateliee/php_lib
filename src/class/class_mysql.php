<?php
//============================================
// class_mysql.php
//============================================

define("MYSQL_MODE_MYSQL",1);
define("MYSQL_MODE_MYSQLI",2);
define("MYSQL_MODE_PDO",3);
//+++++++++++++++++++++++++++++
// DBクラス
//+++++++++++++++++++++++++++++
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
            if((!$result = $this->query($q)) && $debug){
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
//        mysql_query("set names ".$this->charset,$this->linkId);
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
//  }
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
    function fetchArray($result_type = MYSQL_BOTH){
        if($this->numRows() > 0){
            if($this->mysql_mode == MYSQL_MODE_MYSQL){
                $type = MYSQL_BOTH;
                switch($result_type){
                    case MYSQL_BOTH:       $type = MYSQL_BOTH; break;
                    case MYSQL_ASSOC:      $type = MYSQL_ASSOC; break;
                    case MYSQL_NUM:        $type = MYSQL_NUM; break;
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
    function fetchArrayAll(&$list,$result_type = MYSQL_BOTH){
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
            while($value = $this->fetchArray(MYSQL_BOTH)){
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
        if($this->query($sql)){
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
                while( $list = $this->fetchArray( MYSQL_BOTH ) ){
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
    function createTable(){
        $args = func_get_args();
        return call_user_func_array(array($this, "createTableSQL"), $args);
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
    function createIndex(){
        $args = func_get_args();
        return call_user_func_array(array($this, "createIndexSQL"), $args);
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
        if(isset($fields["DEFAULT"]) && $fields["DEFAULT"] != ""){
            $attr[] = "DEFAULT ".$fields["DEFAULT"];
        }
        if(isset($fields["UNIQUE"]) && $fields["UNIQUE"] == true){
            $attr[] = "UNIQUE";
        }
        $sql = "`".$name."` ".$type." ".implode(" ",$attr);
        return $sql;
    }
    function createColumn(){
        $args = func_get_args();
        return call_user_func_array(array($this, "createColumnSQL"), $args);
    }
    // テーブル削除SQL文を作成
    function deleteTableSQL($dbname){
        $sql = 'DROP TABLE `'.$dbname.'` ';
        return $sql;
    }
    function deleteTable(){
        $args = func_get_args();
        return call_user_func_array(array($this, "deleteTableSQL"), $args);
    }
    // カラム追加
    function alterTableSQL($dbname,$name,$column=array(),$after=""){
        $sql = 'ALTER TABLE `'.$dbname.'` ADD '.$this->createColumnSQL($name,$column)." ".$after;
        return $sql;
    }
    function alterTable(){
        $args = func_get_args();
        return call_user_func_array(array($this, "alterTableSQL"), $args);
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
    function insertRecode(){
        $args = func_get_args();
        return call_user_func_array(array($this, "insertRecodeSQL"), $args);
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
    function updateRecode(){
        $args = func_get_args();
        return call_user_func_array(array($this, "updateRecodeSQL"), $args);
    }
    // レコード削除SQL文を作成
    function deleteRecodeSQL($dbname,$where){
        $sql = 'DELETE FROM `'.$dbname.'` ';
        $sql .= $where;
        return $sql;
    }
    function deleteRecode(){
        $args = func_get_args();
        return call_user_func_array(array($this, "deleteRecodeSQL"), $args);
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
    function escape(){
        $args = func_get_args();
        return call_user_func_array(array($this, "escapeSQL"), $args);
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
    function toString(){
        $args = func_get_args();
        return call_user_func_array(array($this, "toStringSQL"), $args);
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
    function toNumber(){
        $args = func_get_args();
        return call_user_func_array(array($this, "toNumberSQL"), $args);
    }
    function toSqlValueSQL($val,$type,$default=NULL){
        $type = strtoupper($type);
        switch($type){
            // 文字列変換
            case 'CHAR':
            case 'VARCHAR':
            case 'TEXT':
            case 'MEDIUMTEXT':
            case 'DATE':
            case 'DATETIME':
            case 'TIMESTAMP':
            case 'TIME':
            case 'YEAR':
                $val = $this->toStringSQL($val,$default);
                break;
            // BLOB型
            case 'TINYBLOB':
            case 'BLOB':
            case 'MEDIUMBLOB':
            case 'LONGBLOB':
                $val = $this->toString($val,$default);
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
    function toSqlValue(){
        $args = func_get_args();
        return call_user_func_array(array($this, "toSqlValueSQL"), $args);
    }
    function toSqlValueListSQL($valuelist,$tablevalue){
        foreach($valuelist as $key => $val){
            //if(in_array($tkey,$valuelist)){
            $tv = $tablevalue[$key];
            if(isset($tv)){
                if(isset($tv["DEFAULT"])){
                    $valuelist[$key] = $this->toSqlValueSQL($val,$tv["TYPE"],$tv["DEFAULT"]);
                }else{
                    $valuelist[$key] = $this->toSqlValueSQL($val,$tv["TYPE"]);
                }
            }
        }
        return $valuelist;
    }
    function toSqlValueList(){
        $args = func_get_args();
        return call_user_func_array(array($this, "toSqlValueListSQL"), $args);
    }
}
// マルチDBクラス
class class_mysql{
    var     $db;
    var     $current;
    var     $auto_connect = true;
    var     $debug = false;

    //====================================
    // コンストラクタ
    //====================================
    function class_mysql(){
        $this->db = array();
        $this->current = 0;
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
    function fetchArray($result_type = MYSQL_BOTH){
        return $this->db[$this->current]->fetchArray($result_type);
    }
    function fetchArrayAll(&$list,$result_type = MYSQL_BOTH){
        return $this->db[$this->current]->fetchArrayAll($list,$result_type);
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
    function createTable(){
        $args = func_get_args();
        return call_user_func_array(array($this, "createTableSQL"), $args);
    }
    // INDEX作成
    function createIndexSQL($dbname,$fields=array()){
        return $this->db[$this->current]->createIndexSQL($dbname,$fields);
    }
    function createIndex(){
        $args = func_get_args();
        return call_user_func_array(array($this, "createIndexSQL"), $args);
    }
    // COLUMN作成
    function createColumnSQL($name,$fields=array()){
        return $this->db[$this->current]->createColumnSQL($dbname,$fields);
    }
    function createColumn(){
        $args = func_get_args();
        return call_user_func_array(array($this, "createColumnSQL"), $args);
    }
    // テーブル削除SQL文を作成
    function deleteTableSQL($dbname){
        return $this->db[$this->current]->deleteTableSQL($dbname);
    }
    function deleteTable(){
        $args = func_get_args();
        return call_user_func_array(array($this, "deleteTableSQL"), $args);
    }
    // カラム追加
    function alterTableSQL($dbname,$name,$column=array(),$after=""){
        return $this->db[$this->current]->alterTableSQL($dbname,$name,$column,$after);
    }
    function alterTable(){
        $args = func_get_args();
        return call_user_func_array(array($this, "alterTableSQL"), $args);
    }
    //----------------------------------------
    // SQL作成
    //----------------------------------------
    // レコード追加SQL文を作成
    function insertRecodeSQL($dbname,$args){
        return $this->db[$this->current]->insertRecodeSQL($dbname,$args);
    }
    function insertRecode(){
        $args = func_get_args();
        return call_user_func_array(array($this, "insertRecodeSQL"), $args);
    }
    // レコード更新SQL文を作成
    function updateRecodeSQL($dbname,$args,$where){
        return $this->db[$this->current]->updateRecodeSQL($dbname,$args,$where);
    }
    function updateRecode(){
        $args = func_get_args();
        return call_user_func_array(array($this, "updateRecodeSQL"), $args);
    }
    // レコード削除SQL文を作成
    function deleteRecodeSQL($dbname,$where){
        return $this->db[$this->current]->deleteRecodeSQL($dbname,$where);
    }
    function deleteRecode(){
        $args = func_get_args();
        return call_user_func_array(array($this, "deleteRecodeSQL"), $args);
    }
    //----------------------------------------
    // データ変換
    //----------------------------------------
    // エスケープ
    function escapeSQL($str){
        return $this->db[$this->current]->escapeSQL($str);
    }
    function escape(){
        $args = func_get_args();
        return call_user_func_array(array($this, "escapeSQL"), $args);
    }
    function toSqlValueListSQL($valuelist,$tablevalue){
        return $this->db[$this->current]->toSqlValueListSQL($valuelist,$tablevalue);
    }
    function toSqlValueList(){
        $args = func_get_args();
        return call_user_func_array(array($this, "toSqlValueListSQL"), $args);
    }
}

?>