<?php

/* Balises trans pour localisation, 10 maxi */
function smarty_modifier_bt($string,$a1=NULL,$a2=NULL,$a3=NULL,$a4=NULL,$a5=NULL,$a6=NULL,$a7=NULL,$a8=NULL,$a9=NULL,$a10=NULL) {
	for ($a=1;$a<11;$a++) {
		if (${"a".$a}===NULL) break;
    	$string = str_replace("{".$a."}",${"a".$a},$string);
	}
	return $string;
}

/* vim: set expandtab: */

?>
