<?php
include_once dirname(__FILE__)."/../libs/ATF/ATF.inc.php";
if ($_GET["sess"]) {
	ATF::define("sessionId",$_GET["sess"]);
}

try{
	include(dirname(__FILE__)."/../global.inc.php");
	ATF::mobile()->cmd($_GET["commande"]);
}catch(errorATF $e){
	log::logger("ERROR : ".$e->getMessage(),'mobile');
//      $e->setError();
}
die();
?>