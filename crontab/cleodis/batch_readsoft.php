<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



$files_recup = array("xml"=>0 , "pdf"=>0 , "autre"=>0, "error"=>0);

$dir = __ABSOLUTE_PATH__."www/readsoft/".$_SERVER["argv"][1]."/";


echo "--------  Batch import READSOFT -------\n";
echo "Le rapport est disponible dans /log/readsoft.log\n";

//ATF::readsoft()->readsoftFileToCleodis($dir, $files_recup);


log::logger("======= Analyse du dossier pending ".$dir."pending/", "readsoft");

ATF::readsoft()->analyseFichiersEnAttente($dir);

log::logger("======= Fin de l'analyse du dossier pending ".$dir."pending/", "readsoft");

log::logger("---------------------------------------", "readsoft");


?>