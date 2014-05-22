<?php

//��������������������������������������������������������������������
//�� [ EMOJI TRANS FUNCTION Ver4.0]
//�� emoji_trans.php - 2009/02/17
//�� Copyright (C) DSPT.NET
//�� http://www.dspt.net/
//��������������������������������������������������������������������
	
/********************** �����ݒ� ***********************/
	//�G�����ϊ��e�[�u���i���΃p�X�j
	$emoji_data = "/emoji/table.tsv";
	
	//PC�p�G�����i�[�t�H���_�i�g�b�v�f�B���N�g������̑��΃p�X�j
	$emoji_img_dir = "/include/emoji/images/";
	
	//���샂�[�h�iPHP����Ăяo���ꍇ��0�ASSI�Ȃ�1�j
	$mode = 0;
	
/********************** �ȉ�����͉��ς��Ȃ��ق������� ***********************/
	
	//�G�����ϊ��e�[�u����z��Ɋi�[
	$emoji_array = array();
	$emoji_array[] = "";
	$contents = @file(dirname(__FILE__).$emoji_data);
	foreach($contents as $line){
		$line = rtrim($line);
		$emoji_array[] = explode("\t", $line);
	}
	
	//S-JIS�R�[�h�ɕϊ�
	function encode($data) {
		$data = @mb_convert_encoding($data, "SJIS", "auto");
		return $data;
	}
	
	//SSI�̏ꍇ�̏���
	if ($mode == 1) {
		$num = $_GET["emoji"]; //���͒l�擾
		include_once (dirname(__FILE__).'/user_agent_carrier.php'); // USER AGENT CARRIER SWITCH
		$agent_carrier = user_agent_carrier($_SERVER["HTTP_USER_AGENT"]);
		emoji($num);
	}
	
	//�g�уL�����A�ɍ��킹�ĊG�������o��
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
