<?
/** 
* @author Quentin JANON <qjanon@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::note_de_frais()->checkEndMonth();