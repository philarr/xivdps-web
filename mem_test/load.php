<?php

loadgamedata("en");
loadgamedata("ja");
loadgamedata("fr");
loadgamedata("de");

function loadgamedata($lang) {

	$a = json_decode(file_get_contents("XIV_Spell_".$lang.".json", true));
	foreach ($a as $key => $value) {
		apc_store($lang.":s:".$key, $value);
	}
	$a = json_decode(file_get_contents("XIV_Buff_".$lang.".json", true));
	foreach ($a as $key => $value) {
		apc_store($lang.":b:".$key, $value);
	}
}

?>