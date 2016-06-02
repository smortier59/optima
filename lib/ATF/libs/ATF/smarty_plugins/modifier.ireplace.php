<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty replace modifier plugin
 *
 * Type:     modifier<br>
 * Name:     replace<br>
 * Purpose:  simple search/replace
 * @link http://smarty.php.net/manual/en/language.modifier.replace.php
 *          replace (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param string
 * @param string
 * @return string
 
 
 
 * @example héhé
 Example for using:

<?php

$search  = 'replaceme';
$text    = 'Please RePlaCeMe, OK?';

// Will print "RePlaCeMe" with red color, but after this it would be "replaceme", not "RePlaCeMe"
$replace = '<font color="#FF0000">'.$search.'</font>';
echo str_ireplace($search, $replace, $text);

// Will print "RePlaCeMe" with red color
$replace = '<font color="#FF0000">$1</font>';
echo ext_str_ireplace($search, $replace, $text);

?>
 
 
 
 
 
 
 */
function smarty_modifier_ireplace($subject, $findme, $replacewith)
{	
	return util::searchHighlight($subject, $findme, $replacewith);
}

/* vim: set expandtab: */

?>
