<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

ATF::societe()->q->reset();
foreach(ATF::societe()->select_all() as $k => $v){
	ATF::societe()->checkMauvaisPayeur($v["id_societe"]);
}
