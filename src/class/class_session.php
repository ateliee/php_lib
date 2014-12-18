<?php
//============================================
// class_session.php
//============================================
// session_regenerate_id
//+++++++++++++++++++++++++++++
// セッションクラス
//+++++++++++++++++++++++++++++
class class_session
{
    // 保持する名前空間名
    var $NameSpace = "";
    //=====================
    // グローバル関数
    //=====================
    // セッション開始
    function start()
    {
        if (!session_id()) {
            @session_start();
        }
        // セッションIDの妥当性を調べる
        if (!isset($_SESSION['_SESSION_CHECK'])) {
            $this->regenerate_id();
            $_SESSION['_SESSION_CHECK'] = array(
                'startTime' => time(),
                'changeTime' => time(),
            );
            // セッション更新
        } else {
            $_SESSION['_SESSION_CHECK']['changeTime'] = time();
        }
    }

    // セッションIDの変更
    function regenerate_id()
    {
        $tmp = $_SESSION;
        session_destroy();
        session_id(md5(uniqid(rand(), true)));
        @session_start();
        $_SESSION = $tmp;
    }

    //=====================
    // 関数
    //=====================
    // 名前空間の定義
    function select($namespace = "")
    {
        if ($namespace != "") {
            $this->NameSpace = $namespace;
            // セッションデータの初期化
            if (isset($_SESSION[$namespace]) == false) {
                $_SESSION[$namespace] = array();
            }
        }
    }

    // 取得関数
    function get($name)
    {
        if (isset($_SESSION[$this->NameSpace][$name])) {
            return $_SESSION[$this->NameSpace][$name];
        }
        return false;
    }

    // 設定関数
    function set($name, $val)
    {
        $_SESSION[$this->NameSpace][$name] = $val;
    }

    // 削除
    function unSession($name)
    {
        unset($_SESSION[$this->NameSpace][$name]);
    }

    // チェック
    function is($name)
    {
        return isset($_SESSION[$this->NameSpace][$name]);
    }

    // セッションの破棄
    function destroy($name)
    {
        // 指定された名前空間のセッションを破棄
        if (isset($name)) {
            if (isset($_SESSION[$this->NameSpace][$name])) {
                $_SESSION[$this->NameSpace][$name] = array();
            }
            // 全てのセッションを破棄
        } else {
            // セッション変数を全て解除する
            if (isset($_SESSION)) {
                $_SESSION = array();
            } else {
                if (function_exists('session_unset')) {
                    session_unset();
                }
            }

            // セッションを切断するにはセッションクッキーも削除する。
            // Note: セッション情報だけでなくセッションを破壊する。
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            // 最終的に、セッションを破壊する
            session_destroy();
        }
    }
}
