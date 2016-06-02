<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::db()->begin_transaction();

// Des existences ne devraient pas être là !

$query = "SELECT * , group_concat( id_parc ) AS parcs, group_concat( etat ) AS etats, group_concat( id_affaire ) AS affaires
FROM `parc`
WHERE existence = 'actif'
GROUP BY serial
HAVING count( * ) >1";
$ar = ATF::db()->sql2array($query);

foreach ($ar as $k=>$i) {
	$affaires = explode(",",$i["affaires"]);
	$parcs = explode(",",$i["parcs"]);
	if ($affaires[0]==$affaires[1]) {
		$id_parc_a_modifier = $parcs[1];
	} elseif ($affaires[0]>$affaires[1]) {
		$id_parc_a_modifier = $parcs[1];
	} else {
		$id_parc_a_modifier = min($parcs);
	}
	
	$q = "UPDATE parc SET existence='inactif' WHERE id_parc=".$id_parc_a_modifier;
	echo $q."\n";
	ATF::db()->query($q);
}


$ar = ATF::db()->sql2array($query);
var_dump($ar);

ATF::db()->commit_transaction();
//ATF::db()->rollback_transaction();


?>