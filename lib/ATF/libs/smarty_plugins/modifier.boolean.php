<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     boolean<br>
 * Date:     17 août 2009
 * Purpose:  Return 'true' if is there a value, or 'false'. There is a mode string that can return the boolean like a string
 * Input:<br>
 *         - string = contents to test
 *         - bool = set mode bool on/off
 * Example:  {$text|boolean} or {$text|boolean:'false'}
 * @version  1.0
 * @author   Quentin JANON <qjanon@absystech.fr>
 * @param string
 * @return string
 */
function smarty_modifier_boolean($string,$bool=true)
{
    if ($bool) {
		return $string?true:false;
	} else {
		return $string?'true':'false';
	}
}

?>
