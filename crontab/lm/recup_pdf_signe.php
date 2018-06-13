<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

$id_affaire = $_SERVER['argv'][2];

ATF::slimpay()->recup_pdf_slimpay($id_affaire);

?>