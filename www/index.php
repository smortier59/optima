<?
try{
	include(dirname(__FILE__)."/../global.inc.php");
	
	//Cas particulier pour le keeponline
	if ($_GET["keeponline"]==1) die();

	//Première connexion
	if (count($_GET)===0 && $_SESSION["user"]->logged) {
		header("Location: accueil.html");
	} else {
		//Write session
		ATF::getEnv()->commitSession();
		//ATF::$html->maj_globals();
		
		//Génération du template
		if (ATF::$mobile->isMobile() || $_GET["mobile"]) {
			ATF::$html->displayWithAnalyzer("mobile/body.tpl.htm");
		} else {
			ATF::$html->displayWithAnalyzer("body.tpl.htm");
		}
//log::logger("END","jgwiazdowski");
	}
}catch(errorATF $e){
	$e->setError();
}
?>
