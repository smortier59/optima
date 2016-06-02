<?
/** Recalcul les soldes des gestion ticket en cas de déplacement d'une société a une autre
* @author Quentin JANON <qjanon@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

$id_societe = 678;

ATF::gestion_ticket()->q->reset()->where('id_societe',$id_societe)->addOrder('date');

$gt = ATF::gestion_ticket()->sa();
foreach ($gt as $k=>$i) {
    if (isset($lastSolde)) {
        echo "Ancien solde : ".$i['solde']."; Nouveau solde : ".$lastSolde."+".$i['nbre_tickets']."=".($lastSolde+$i['nbre_tickets'])."\n";
        
        $i['solde'] = $lastSolde+$i['nbre_tickets'];
    }
    $i['operation'] = $k+1;
    ATF::gestion_ticket()->u($i);  
    echo "Affectation du solde : ".$lastSolde." => ".$i['solde']."\n\n";
    $lastSolde = $i['solde'];  
}

?>