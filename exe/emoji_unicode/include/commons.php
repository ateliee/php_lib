<?php

//��������������������������������������������������������������������
//�� [ INCLUDE FILE COMMONS Ver1.0L]
//�� commons.php - 2009/01/27
//�� Copyright (C) DSPT.NET
//�� http://www.dspt.net/
//��������������������������������������������������������������������
	
	//���ʃt�@�C���̃C���N���[�h�i���΃p�X�j
	include_once (dirname(__FILE__).'/user_agent_carrier.php'); // USER AGENT CARRIER SWITCH
	include_once (dirname(__FILE__).'/emoji_trans.php'); //  EMOJI TRANS FUNCTION

	//�g�уL�����A����
	$agent_carrier = user_agent_carrier($_SERVER["HTTP_USER_AGENT"]);
	
?>
