<?php
//============================================
// htaccess.php
//============================================
// 特定の拡張子を許可しない
$hta .= '<Files ~ "\.(gif|jpg|png)$">'."\n";
$hta .= 'deny from all'."\n";
$hta .= '</Files>'."\n";
// モバイルページへ飛ばす
$hta .= 'RewriteEngine On'."\n";
$hta .= 'RewriteCond %{HTTP_USER_AGENT} ^(DoCoMo|KDDI|DDIPOCKET|UP\.Browser|J-PHONE|Vodafone|SoftBank)'."\n";
$hta .= 'RewriteRule ^$ /m/ [R]'."\n";

$hta .= 'Options +FollowSymLinks'."\n";
$hta .= 'RewriteEngine on'."\n";
$hta .= 'RewriteCond %{THE_REQUEST} ^.*/index.php'."\n";
$hta .= 'RewriteRule ^(.*)index.php$ http://as76.net/$1 [R=301,L]'."\n";
?>