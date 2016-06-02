<?
/*
*	Serveur de messagerie instantanée
*/
try{
	include(dirname(__FILE__)."/../global.inc.php");
	
	new socketer();
}catch(error $e){
	$e->setError();
}
?>