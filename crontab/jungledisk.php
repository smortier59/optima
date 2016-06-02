<?
/** Process des jungledisk, et envoie les emails
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../global.inc.php");
ATF::jungledisk()->parseMailbox();
?>