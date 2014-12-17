<?php
//============================================
// conf_system.php
//============================================
//+++++++++++++++++++++++++++++
// �萔�錾
//+++++++++++++++++++++++++++++
// �V�X�e���f�[�^
if (defined('SYSTEM_REQUEST') == false) {
    define('SYSTEM_REQUEST', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null);
}
if (defined('SYSTEM_DOMAIN') == false) {
    define('SYSTEM_DOMAIN', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null);
}
