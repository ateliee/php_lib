<?php

//┌─────────────────────────────────
//│ [ EMOJI TRANS FUNCTION Ver4.0]
//│ emoji_trans.php - 2009/02/17
//│ Copyright (C) DSPT.NET
//│ http://www.dspt.net/
//└─────────────────────────────────
	
/********************** 初期設定 ***********************/
	//絵文字変換テーブル（相対パス）
	$emoji_data = "/emoji/table.tsv";
	
	//PC用絵文字格納フォルダ（トップディレクトリからの相対パス）
	$emoji_img_dir = "/include/emoji/images/";
	
	//動作モード（PHPから呼び出す場合は0、SSIなら1）
	$mode = 0;
	
/********************** 以下からは改変しないほうが無難 ***********************/
	
	//絵文字変換テーブルを配列に格納
	$emoji_array = array();
	$emoji_array[] = "";
	$contents = @file(dirname(__FILE__).$emoji_data);
	foreach($contents as $line){
		$line = rtrim($line);
		$emoji_array[] = explode("\t", $line);
	}
	
	//S-JISコードに変換
	function encode($data) {
		$data = @mb_convert_encoding($data, "SJIS", "auto");
		return $data;
	}
	
	//SSIの場合の処理
	if ($mode == 1) {
		$num = $_GET["emoji"]; //入力値取得
		include_once (dirname(__FILE__).'/user_agent_carrier.php'); // USER AGENT CARRIER SWITCH
		$agent_carrier = user_agent_carrier($_SERVER["HTTP_USER_AGENT"]);
		emoji($num);
	}
	
	//携帯キャリアに合わせて絵文字を出力
	function emoji($data) {
		global $agent_carrier,$emoji_array,$emoji_img_dir;
		if(preg_match("/[0-9]{1,3}/", $data) && is_numeric($data) && 0 < $data && $data < 254) {
			if ($agent_carrier == 'i'){
				$put = "&#x".encode($emoji_array[$data][1]).";";
			}
			elseif ($agent_carrier == 'e'){
				$put = "&#x".encode($emoji_array[$data][2]).";";
			}
			elseif ($agent_carrier == 's'){
				$put = "&#x".encode($emoji_array[$data][3]).";";
			} else {
				$put = "<img src=\"".$emoji_img_dir.$emoji_array[$data][0].".gif\" width=\"12\" height=\"12\" border=\"0\" alt=\"\" />";
			}
		}
		else {
			$put = "[Error!]\n";
		}
		echo $put;
	}

?>
