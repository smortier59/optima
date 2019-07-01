<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



echo "-----------------------------------------------------------------\n";
echo "Début du script de controle du nombre de licence\n";
echo "Pour voir les logs --> /log/controle_licence_bdomplus\n";
ATF::licence()->controle_stock();
echo "Fin du script\n";
echo "-----------------------------------------------------------------\n";


?>