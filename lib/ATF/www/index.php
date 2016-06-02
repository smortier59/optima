<?php
try{
	include("../global.inc.php");
	if ($_GET["keeponline"]==1) die();
	if (count($_GET)===0 && ATF::$usr->logged) {
		// Page d'accueil si on essai d'aller sur "/" en étant déjà loggué...
		header("Location: accueil.html");
		die;
	} else {
//		ATF::$html->maj_globals();
		ATF::$html->displayWithAnalyzer("body.tpl.htm");
	}
//Gestion des erreurs
}catch(Exception $e){
	ATF::errorProcess($e);
}
?>