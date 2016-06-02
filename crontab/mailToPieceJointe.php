<?

/*																						*\
**		Ce script a besoin de paramètre pour fonctionner. Il prend dans l'ordre:		**
**		- La société																	**
**		- La classe concernée															**
**		- Suffixe du nom de fichier														**
\*																						*/
$argv = $_SERVER["argv"];

define("__BYPASS__",true);
$_SERVER["argv"][1] = $argv[1];
include(dirname(__FILE__)."/../global.inc.php");


ATF::classes_optima()->mailToPieceJointe($argv[2], $argv[3], $argv[1]);
?>
