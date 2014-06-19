<?php
//============================================
// class_CSRF.php
// CSRF:クロスサイトリクエストフォージェリ(Cross-Site Request Forgeries)
// CSR:特定のサイトの正規のユーザの権限を悪用して、正規のユーザが意図していない処理を強制させる攻撃
//============================================

//+++++++++++++++++++++++++++++
// class_CSRFクラス
//+++++++++++++++++++++++++++++
class class_CSRF
{
    var $ttl;
    var $name;

    function Token( $name = 'tokens', $ttl = 1800 )
    {
      // CSRF 検出トークン最大有効期限(秒)
      // 最小期限はこの値の 1/2 (1800 の場合は、900秒間は最低保持される)
      $this->ttl = (int)$ttl;

      // セッションに登録するトークン配列の名称
      $this->name = $name;
    }

    /**
     * トークンを生成
     */
    function createToken()
    {
      $curr = time();
      $tokens = isset( $_SESSION[$this->name] ) ? $_SESSION[$this->name] : array();
      foreach ( $tokens as $id => $time ) {
          // 有効期限切れの場合はリストから削除
          if ( $time < $curr - $this->ttl ) {
            unset( $tokens[$id] );
          }
          else {
            $uniq_id = $id;
          }
      }
      if ( count( $tokens ) < 2 ) {
          if ( ! $tokens || ( $curr - (int)( $this->ttl / 2 ) ) >= max( $tokens ) ) {
            $uniq_id = sha1( uniqid( rand(), TRUE ) );
            $tokens[$uniq_id] = time();
          }
      }
      // リストをセッションに登録
      $_SESSION[$this->name] = $tokens;
      return $uniq_id;
    }

    /**
     * セッションのリストにトークンが存在し、トークンが有効期限内の場合は FALSE を返す
     */
    function isCSRF( $token )
    {
      $tokens = $_SESSION[$this->name];
      if ( isset( $tokens[$token] ) && $tokens[$token] > time() - $this->ttl ) {
          return FALSE;
      }
      return TRUE;
    }
}

?>