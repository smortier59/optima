<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "ginger";
include_once dirname(__FILE__)."/../../global.inc.php";

ATF::tache()->rappel();

//rappeler les acteurs qui n'ont pas donné leur avis sur l'opportunite
ATF::opportunite_user()->rappel();
	
?>