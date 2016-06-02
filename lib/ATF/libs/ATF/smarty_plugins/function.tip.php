<?php
function smarty_function_tip($params, $smarty, $template)
{
	if (!$params["content"]) {
		$params["content"] = $params["text"];
		unset($params["text"]);
	}
	
    if (empty($params["content"])) {
        $smarty->trigger_error("overlib: attribute 'text' or 'content' required");
        return false;
    }
	
	if ($params["rel"]) {
		$htm = ' rel="'.preg_replace(array('!"!',"![\r\n]!"),array("'",'\r'),$params["content"]).'"';
		if ($params["title"]) {
			$htm .= ' title="'.preg_replace(array('!"!',"![\r\n]!"),array("'",'\r'),$params["title"]).'"';
		}
		return $htm;
	} else {
		if (!$params["id"] && !$params["class"]) {
			$params["id"] = 'this';
		} elseif ($params["id"]) {
			$params["id"] = "'".$params["id"]."'";
		}
		
		$default["position"] = 'rightTop';
		$default["borderSize"] = 3;
		$default["radius"] = 3;
		$default["css"] = 'ex3';
		//$default["fadeDuration"] = .25;
		$default["delay"] = .3;
		
		$parameters = array_merge($default,$params);
		unset($parameters["content"],$parameters["id"],$parameters["class"]);
		if ($parameters["title"]) {
			$parameters["title"] = htmlentities($parameters["title"],ENT_QUOTES,"UTF-8");
		}
		$json = preg_replace(array('!"!',"![\r\n]!"),array("'",'\r'),json_encode($parameters));
		
		if (isset($params["assign"])) {
			$smarty->assign($params["assign"], $htm);
		} else {
			return $htm;
		}
	}
}
?>