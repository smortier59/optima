<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$filepath = __FILE__.".sql";
@unlink($filepath);

ATF::db()->begin_transaction();



$s = file_get_contents("/home/absystech/optima.absystech.net/core/log/preventionDoublonsActifs");
$r = preg_match_all("/\| ([0-9]*)\]/",$s,$matches);
print_r($matches[1]);
foreach ($matches[1] as $id_parc) {
	ATF::parc()->update(array("id_parc"=>$id_parc,"existence"=>"actif","date_inactif"=>""));
}

ATF::affaire()->q->reset();
if ($affaires = ATF::affaire()->sa()) {
	foreach($affaires as $affaire){
		//print_r($affaire);
		ATF::parc()->preventionDoublonsActifs($affaire["id_affaire"]);
	}
}


// echo "\ncommande :\n";
// $query = "SELECT parc . * , s.societe, group_concat( a.id_affaire ) AS affaires, group_concat( a.ref ) AS refaffaires, count( * ) AS nb, group_concat( parc.etat ) AS etats
// FROM `parc`
// LEFT JOIN affaire a ON a.id_affaire = parc.id_affaire
// LEFT JOIN societe s ON s.id_societe = a.id_societe
// WHERE existence = 'actif'
// GROUP BY serial
// HAVING count( * ) >1
// ORDER BY nb DESC";
// $r = ATF::db()->sql2array($query);
// foreach ($r as $item) {
	// // On récupère les parcs de ce serial
	// $query = "SELECT * FROM parc WHERE serial='".$item["serial"]."' AND existence='actif' ORDER BY id_affaire DESC, id_parc DESC";
	// $r2 = ATF::db()->sql2array($query);	
	// foreach ($r2 as $key2 => $item2) {
		// //print_r($item2)."\n";
		// if (!$key2) {
			// echo "[".$item["serial"]." / ".$item["id_affaire"]."] on ne garde que l'etat actif '".$item2["etat"]."'"."\n";			
		// } else {
			// echo "[".$item["serial"]." / ".$item["id_affaire"]."] on passe en inactif l'état '".$item2["etat"]."'"."\n";		
			// $query = "UPDATE parc SET existence='inactif' WHERE id_parc=".$item2["id_parc"].";\n";
			// file_put_contents($filepath,$query,FILE_APPEND);
			// //$r2 = ATF::db()->sql2array($query);	
		// }
// 		
	// }
// 	
// }
// 
// die;

ATF::db()->commit_transaction();
