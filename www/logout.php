<?
/**
* Script de déconnexion de l'application
*/
include(dirname(__FILE__)."/../global.inc.php");
try{
	//Déconnexion de l'utilisateur
	if (ATF::$usr->logged) {
		ATF::$usr->logout();
		//Write de la session
		ATF::getEnv()->commitSession();
	}
	header("Location: /");
}catch(error $e){
	$e->setError();
}
?>