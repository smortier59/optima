<?

define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
require(dirname(__FILE__)."/../../includes/absystech/cout_copie.class.php");

$cout = new cout_copie();

//$cout->parse();

$cout->get_all_facture();

?>