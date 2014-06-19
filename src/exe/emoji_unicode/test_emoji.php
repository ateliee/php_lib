<?php
	//‹¤’Êƒtƒ@ƒCƒ‹‚Ì“Ç‚İ‚İi‘Š‘ÎƒpƒXj
	include_once './include/commons.php';
	
	function test($hex){
		$dec = hexdec($hex);
		if (58942 <= $dec AND $dec <= 59035) {
			// ŠG•¶šNo.1 ` No.94
			$dec = $dec + 4705;
		} elseif (59099 <= $dec AND $dec <= 59223) {
			// ŠG•¶šNo.118 ` No.166AŠg1`Šg76
			$dec = $dec + 4773;
		} elseif ((59036 <= $dec AND $dec <= 59045) OR
			(59052 <= $dec AND $dec <= 59054) OR
			(59057 <= $dec AND $dec <= 59059) OR
			(59063 <= $dec AND $dec <= 59066) OR
			(59086 <= $dec AND $dec <= 59098)) {
			// ŠG•¶šNo.95 ` No.117ANo.167 ` No.176
			$dec = $dec + 4772;
		} else {
			return '';
		}
		return $dec;
	}
?>
<html>
<head>
<title>Œg‘ÑŠG•¶š•ÏŠ·ƒXƒNƒŠƒvƒg</title>
</head>
<body>
<?php
	for($i=1;$i<254;$i++){
		$c = $emoji_array[$i][1];
		print "&#".test($c).";<br />\n";
//		print "'".$i."' => array( '".test($c)."','".$c."' ),<br />";
	}
?>
</body>
</html>