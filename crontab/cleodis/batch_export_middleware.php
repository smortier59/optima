<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


echo "--------  Batch export MIDDLEWARE -------\n";

ATF::pack_produit()->export_middleware(array("export_from_batch"=> true));


echo "--------  Fin du batch  -------\n";