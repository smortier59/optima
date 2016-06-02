<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_modifier_acronyme($str,$separator="") {
    $words = explode(" ",$str);
	array_walk($words,create_function('&$s', '$s = ucfirst(substr($s,0,1));'));
	return implode($separator,$words);
}
?>