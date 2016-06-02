<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

//Permet d'importer tous les filtres Privé et stats du custom pour les mettre dans la base
ATF::$usr->importFiltrePrive();
?>