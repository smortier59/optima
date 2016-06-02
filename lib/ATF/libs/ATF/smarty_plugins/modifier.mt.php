<?php
# /*
# * Smarty plugin
# * -------------------------------------------------------------
# * Fichier : 	modifier.mt.php
# * Type :		modifier
# * Nom :		mt
# * Rôle :    	renvoie la phrase modifiee avec les parametres fournis
# * -------------------------------------------------------------
# */
function smarty_modifier_mt($string,$remp)
{
	$pattern = '/\{(.[^\}]*)\}/';
	$matches = array();
	preg_match_all($pattern, $string, $matches);
	
	foreach ($matches[0] as $k => $i) {
		$string = str_replace($i,$remp[$matches[1][$k]],$string);
	}
	return utf8_decode(utf8_encode($string));
} 

?>