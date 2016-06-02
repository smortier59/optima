<?php
/**
 * Smarty {modalbox} function plugin
 *
 * Type:     function<br>
 * Name:     modalbox<br>
 * Purpose:  print out a modalbox
 * @author Yann GAUTHERON <abalam at aewd dot net>
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_modalbox($params, $smarty, $template)
{
    if (!isset($params['template']) && !isset($params['text'])) {
		$smarty->_trigger_fatal_error("[modalbox] parameter 'template' AND 'text' cannot be empty, please add at least a small text.");
		return;
    }
	
	//$json = "{ title: '', params: 'template='.$template.'', method:'post' }";
	if (isset($params["title"])) {
		$params["title"] = htmlentities($params["title"],ENT_QUOTES);
	}
	if ($tpl = $params["template"]) {
		unset($params["template"]);
		$js = 'Modalbox.show("'.$tpl.'.dialog", '.json_encode($params).');';
	} elseif ($text = $params["text"]) {
		unset($params["text"]);
		$js = 'Modalbox.show("<div>'.htmlentities($text,ENT_QUOTES).'</div>", '.json_encode($params).');';
	}
	
	// Icone
	$icon = "images/icones/help.png";
	if ($params["icon"]) {
		$icon = $params["icon"];
	}
	
	return '<img src="'.$icon.'" style="cursor:pointer" onclick=\''.$js.'\' />';
}

/* vim: set expandtab: */

?>
