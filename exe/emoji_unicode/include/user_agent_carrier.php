<?php

//┌─────────────────────────────────
//│ [ USER AGENT CARRIER SWITCH FUNCTION Ver1.0]
//│ user_agent_carrier.php - 2009/01/27
//│ Copyright (C) DSPT.NET
//│ http://www.dspt.net/
//└─────────────────────────────────
	
	//携帯端末のユーザエージェントから携帯キャリアを判定
	function user_agent_carrier($data){
		if(preg_match("/^DoCoMo\/[12]\.0/i", $data))
		{
    		return "i";// i-mode
		}
		elseif(preg_match("/^(J\-PHONE|Vodafone|MOT\-[CV]980|SoftBank)\//i", $data))
		{
    		return "s";// softbank
		}
		elseif(preg_match("/^KDDI\-/i", $data) || preg_match("/UP\.Browser/i", $data))
		{
    		return "e";// ezweb
		}
		elseif(preg_match("/^PDXGW/i", $data) || preg_match("/(DDIPOCKET|WILLCOM);/i", $data))
		{
    		return "w";// willcom
		}
		elseif(preg_match("/^L\-mode/i", $data))
		{
    		return "l";// l-mode
		}
		else {
    		return "p";// pc
		}
	}

?>
