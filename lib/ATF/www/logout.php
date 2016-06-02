<?php
/**
* Script de dconnexion de l'application
*/
//Inclusion du framework
include("../global.inc.php");
try{
	//L'utilisateur est logg
	if (ATF::$usr->logged) {
		ATF::usr()->logout();
		//ATF::$msg->add('La dconnexion a russie !');
	}else{
	//L'utilisateur n'est pas logg
		//ATF::$msg->add("Vous n'tiez pas logg, retour  la page d'acceuil");
	}
	header("Location: ".__MANUAL_WEB_PATH__);
}catch(errorATF $e){
	$e->setError();
}
?>