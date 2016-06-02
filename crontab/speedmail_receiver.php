<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");
error_reporting(E_ALL);

$bases = ATF::db()->sql2array("SHOW DATABASES WHERE `Database` LIKE 'extranet_v3_%'");

foreach ($bases as $k => $i) {
	if ($tableExist = ATF::db()->sql2array("SHOW TABLES FROM `".$i['Database']."` LIKE 'emailing_%'")) {
		echo "\nReceiver sur mail(s) envoyé sur ".$i['Database'];
		ATF::$codename = substr($i['Database'],12);
		ATF::emailing_job()->check_retour();
	}
}


?>