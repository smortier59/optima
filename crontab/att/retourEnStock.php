<?

/** 
* Script qui remet en stock tous les produit issu d'un fichier CSV
* @author Quentin JANON <qjanon@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "att";
include(dirname(__FILE__)."/../../global.inc.php");

$fichier = "serials.csv";
$fic = fopen($fichier, 'rb');

$entete = fgetcsv($fic);
ATF::db()->begin_transaction(true);

try {
    // Changement d'état des téléphones
    while ($ligne = fgetcsv($fic)) {
        ATF::stock()->q->reset()->where("serial",$ligne[0])->where("adresse_mac",$ligne[1]);
        $ct += count(ATF::stock()->sa());
        $s = ATF::stock()->select_row();
        echo "\n".$ligne[0]."/".$ligne[1]." => ".$s['stock.id_stock']."\n";
    
        $etat = array(
            "id_stock"=>$s['stock.id_stock'],
            "etat"=>"stock",
            "commentaire"=>"Routine retourEnStock.php éxécuté par Quentin"
        );
        ATF::stock_etat()->insert($etat);
        
    }   
    
    // Changement d'état des bloc d'alim
    ATF::stock()->q->reset()->where("id_affaire",496)->where("ref","87-00012AAA-A")->setLimit(51);
    $bloc = ATF::stock()->sa();
    echo count($bloc)." Bloc d'alim !\n";
    foreach ($bloc as $k=>$i) {
        $etat = array(
            "id_stock"=>$i['id_stock'],
            "etat"=>"stock",
            "commentaire"=>"Routine retourEnStock.php éxécuté par Quentin"
        );
        ATF::stock_etat()->insert($etat);    
    }
} catch (error $e) {
    ATF::db()->rollback_transaction(true);
}
ATF::db()->commit_transaction(true);



echo "DONE !\n";
?>