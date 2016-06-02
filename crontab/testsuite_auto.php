<?php
$u = trim(`id -u`);
if ($u!="81") {
	die("Les TU ne peuvent être lancé qu'en user apache !");
}

include_once dirname(__FILE__)."/../global.inc.php";
testsuite::execute_auto();
