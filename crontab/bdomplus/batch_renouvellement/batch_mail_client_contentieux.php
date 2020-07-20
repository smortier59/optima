<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);


ATF::affaire()->process_envoi_mail_non_renouvellement_client_contentieux();

?>