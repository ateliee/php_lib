<?php
//============================================
// conf_system.php
//============================================
//+++++++++++++++++++++++++++++
// �萔�錾
//+++++++++++++++++++++++++++++
// �V�X�e���f�[�^
if (defined('SYSTEM_REQUEST') == false) {
    define('SYSTEM_REQUEST', $_SERVER['REQUEST_URI']);
}
if (defined('SYSTEM_DOMAIN') == false) {
    define('SYSTEM_DOMAIN', $_SERVER['SERVER_NAME']);
}
?>