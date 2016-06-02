<?php
/**
* Script de debug, uniquement pour le développement
*/
/* Démarrage de la session */
include("../global.inc.php");
session_name(__SESSION_NAME__);
session_set_cookie_params(86400*7,'/',$_SERVER['HTTP_HOST'],true,true);
session_start();
	switch ($_GET["cmd"]) {
		case "flush":
			$sessions=array("ATF","pager","tpl_exists");
			foreach($sessions as $session){
				if($_SESSION[$session]){
					unset($_SESSION[$session]);
				}
			}
			echo "Singletons supprimes, session restante : (que le user normalement !) ";
			print_r($_SESSION);
			break;
		case "flushUser":
			session_destroy();
			break;
		case "pdf":
			$pdf = new pdf_absystech;
			$pdf->generic("etiquette_logo");
			break;
		case "showS":
			print_r($_SESSION);
			break;
	}
?>