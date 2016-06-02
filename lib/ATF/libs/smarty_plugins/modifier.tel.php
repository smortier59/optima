<?php
function smarty_modifier_tel($string)
{	
	$string = str_replace(" ","",trim($string));
	if (strlen($string)==10) { // Numéro français
		return substr($string,0,2)." ".substr($string,2,2)." ".substr($string,4,2)." ".substr($string,6,2)." ".substr($string,8,2);
	}
	return $string;
} 

?>