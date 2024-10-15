<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__.$_SERVER["argv"][1]."/";

echo "GENERATION PDF DEVIS ";
$i = 'SELECT id_devis, id_affaire FROM `devis`';
foreach (ATF::db()->sql2array($i) as $value) {
    ATF::devis()->move_files($value["id_devis"]);
}
echo ": DONE\n";


echo "GENERATION PDF FACTURE ";
$i = 'SELECT id_facture FROM `facture`';
foreach (ATF::db()->sql2array($i) as $value) {
    ATF::facture()->move_files($value["id_facture"]);
}
echo ": DONE\n";

echo "GENERATION PDF FACTURE FOURNISSEUR ";
$i = 'SELECT id_facture_fournisseur FROM `facture_fournisseur`';
foreach (ATF::db()->sql2array($i) as $value) {
    ATF::facture_fournisseur()->move_files($value["id_facture_fournisseur"]);
}
echo ": DONE\n";

echo "GENERATION PDF BON DE COMMANDE ";
$i = 'SELECT id_bon_de_commande FROM `bon_de_commande`';
foreach (ATF::db()->sql2array($i) as $value) {
    ATF::bon_de_commande()->move_files($value["id_bon_de_commande"]);
}
echo ": DONE\n";

?>