<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {t} function plugin
 *
 * Type:     function<br>
 * Name:     t<br>
 * Purpose:  make transaction
 * @author   Jérémie gwiazdowski <jgw at absystech dot fr>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_t($params, $smarty, $template)
{
	return $smarty->getUser()->trans($params["w"],$params["p"],$params["s"],$params["strict"]);
}

/* vim: set expandtab: */

?>