<?php
function smarty_modifier_mod_rewrite($string) {
	$search  = "חסְֱֲִהגאבaֻÊָֹיטכךֿ־ּֽןמלםiײװׂ׃צפעףÜÛÙÚüûשתuµ@µ$£ֳ";
	$replace = "cnAAAAaaaaaEEEEeeeeIIIIiiiiiOOOOooooUUUUuuuuuuauSLas";
	$string = utf8_encode(strtr(utf8_decode($string), $search, $replace));
	
	if ($array = str_split($string)) {
		foreach ($array as $s) {
			if (!preg_match("/[a-zA-Z0-9]/",$s)) {
				$string = str_replace($s,"-",$string);
			}
		}
	}
	do {
		$old_length = strlen($string);
		$string = str_replace("--","-",$string);
	} while ($old_length != strlen($string));
	
	if (substr($string,0,1)=="-") {
		$string = substr($string,1);
	}
	
    return $string;
}
?>