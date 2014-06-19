<?php
//============================================
// init_lib.php
//============================================
      // カレントディレクトリの変更
      $lib_beforeCwd = getcwd();
      if( chdir(dirname(__FILE__)) == false ){
            exit();
      }
      //+++++++++++++++++++++++++++++
      // 定義
      //+++++++++++++++++++++++++++++
      //set_include_path() 
      // バージョン
      define('SYSTEM_VERSION','1.0');
      define('SYSTEM_UPDATE','2011/05/05');
      // 文字コード
      define('SYSTEM_LANGUAGE','uni');
      define('SYSTEM_SERVER_ENCODE','Shift-JIS');
      define('SYSTEM_HTML_ENCODE','UTF-8');
      define('SYSTEM_DB_ENCODE','UTF-8');
      define('SYSTEM_PHP_ENCODE','UTF-8');
      define('SYSTEM_MOBILE_ENCODE','Shift-JIS');
      //define('SYSTEM_MAIL_ENCODE','Shift-JIS');
      define('SYSTEM_MAIL_ENCODE','SJIS');
      define('SYSTEM_ZIP_ENCODE','Shift-JIS');
      // システム言語
      define('SYSTEM_LIB_LANGUAGE','ja');
      // パスデータ
      define('SYSTEM_PATH_COF',"conf/");
      define('SYSTEM_PATH_FUNC',"func/");
      define('SYSTEM_PATH_CLASS',"class/");
      define('SYSTEM_PATH_EXE',"exe/");
      define('SYSTEM_PATH_LANGUAGE',"language/");
      // デフォルトタイムゾーン
      define('SYSTEM_DEFAULT_TIMEZONE',"Asia/Tokyo");
      
      // 文字コード設定
      mb_language(SYSTEM_LANGUAGE);
      mb_internal_encoding(SYSTEM_HTML_ENCODE);
      mb_http_input(SYSTEM_HTML_ENCODE);
      mb_http_output(SYSTEM_HTML_ENCODE);
      // PHP5
      if (function_exists('date_default_timezone_set')){
            // デフォルトタイムゾーン
            date_default_timezone_set(SYSTEM_DEFAULT_TIMEZONE);
      }
      //+++++++++++++++++++++++++++++
      // ライブラリ
      //+++++++++++++++++++++++++++++
      // MagpieRSS
      //define('MAGPIE_CACHE_DIR', dirname(__FILE__) . '/magpierss/cache');          // キャッシュフォルダ
      //define('MAGPIE_CACHE_AGE', 3600); // cacheの寿命
      //define('MAGPIE_OUTPUT_ENCODING', SYSTEM_PHP_ENCODE);    // 文字コード
      //define('MAGPIE_FETCH_TIME_OUT', 5); // 接続をタイムアウトする時間
      
      //include_once( dirname(__FILE__) . '/library/magpierss/rss_fetch.inc' );
      //include_once( dirname(__FILE__) . '/library/magpierss/rss_utils.inc' );
      //include_once( dirname(__FILE__) . '/library/magpierss/config.php' );
      
      include_once( dirname(__FILE__) . '/library/simplehtmldom/simple_html_dom.php' );
      //+++++++++++++++++++++++++++++
      // クラス宣言
      //+++++++++++++++++++++++++++++
      // php4用
      include_once( SYSTEM_PATH_EXE.'upgrade.php' );
      // config
      include_once( SYSTEM_PATH_COF.'conf_define.php' );
      include_once( SYSTEM_PATH_COF.'conf_word.php' );
      include_once( SYSTEM_PATH_COF.'conf_system.php' );
      // class
      include_once( SYSTEM_PATH_CLASS.'class_system.php' );
      include_once( SYSTEM_PATH_CLASS.'class_cookie.php' );
      include_once( SYSTEM_PATH_CLASS.'class_session.php' );
      include_once( SYSTEM_PATH_CLASS.'class_template.php' );
      include_once( SYSTEM_PATH_CLASS.'class_file.php' );
      include_once( SYSTEM_PATH_CLASS.'class_string.php' );
      include_once( SYSTEM_PATH_CLASS.'class_mail.php' );
      include_once( SYSTEM_PATH_CLASS.'class_mysql.php' );
      include_once( SYSTEM_PATH_CLASS.'class_html.php' );
      include_once( SYSTEM_PATH_CLASS.'class_image.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_calender.php' );
      include_once( SYSTEM_PATH_CLASS.'class_rss.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_qr.php' );
      include_once( SYSTEM_PATH_CLASS.'class_mobile.php' );
      include_once( SYSTEM_PATH_CLASS.'class_CSRF.php' );
      include_once( SYSTEM_PATH_CLASS.'class_xml.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_socket.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_ftp.php' );
      include_once( SYSTEM_PATH_CLASS.'class_yahooAPI.php' );
      include_once( SYSTEM_PATH_CLASS.'class_rakutenAPI.php' );
      include_once( SYSTEM_PATH_CLASS.'class_amazonAPI.php' );
      include_once( SYSTEM_PATH_CLASS.'class_youtubeAPI.php' );
      include_once( SYSTEM_PATH_CLASS.'class_wikipediaAPI.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_zip.php' );
      include_once( SYSTEM_PATH_CLASS.'class_twitter.php' );
      include_once( SYSTEM_PATH_CLASS.'class_facebook.php' );
      include_once( SYSTEM_PATH_CLASS.'class_blogger.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_imap.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_apns.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_gcm.php' );
      include_once( SYSTEM_PATH_CLASS.'class_flickrAPI.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_pdf.php' );
      //include_once( SYSTEM_PATH_CLASS.'class_moshimoAPI.php' );
      // function
      include_once( SYSTEM_PATH_FUNC.'func_php.php' );
      include_once( SYSTEM_PATH_FUNC.'func_system.php' );
      include_once( SYSTEM_PATH_FUNC.'func_header.php' );
      include_once( SYSTEM_PATH_FUNC.'func_agent.php' );
      include_once( SYSTEM_PATH_FUNC.'func_language.php' );
      include_once( SYSTEM_PATH_FUNC.'func_html.php' );
      include_once( SYSTEM_PATH_FUNC.'func_security.php' );
      
      // ディレクトリを戻す
      chdir($lib_beforeCwd);
      
      // メモリ
      ini_set("memory_limit", "512M");
      // タイムアウトの時間
      set_time_limit(120);
      // アップロードサイズを設定
      ini_set('upload_max_filesize', "256M");
      
      // 自動エスケープを戻す
      if (get_magic_quotes_gpc()){
            function f_reMagicQuotes($args){
                  if(is_array($args)){
                        foreach($args as $key => $val){
                              $args[$key] = f_reMagicQuotes($val);
                        }
                  }else{
                        $args = stripslashes($args);
                  }
                  return $args;
            }
            $_POST = f_reMagicQuotes($_POST);
            $_GET = f_reMagicQuotes($_GET);
      }
?>