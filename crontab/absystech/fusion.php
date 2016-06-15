<?
/** Fusion d'élément
* @author Quentin JANON <qjanon@absystech.fr>
*/
if ($_SERVER["argc"]!=5) {
	echo "Probleme de syntaxe \n";
	echo "\"php fusion.php [CODENAME] [TABLE] [IDto] [IDFrom]\"\n";
	echo "Cette syntaxe va fusionner l'element de la table TABLE qui a l'id IDFrom vers l'element qui a l'id IDTo\n";
	die();
}

define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

ATF::db()->begin_transaction(true);
try {
	$racine = ATF::$_SERVER["argv"][2]()->select($_SERVER["argv"][3]);
	$toFusion = ATF::$_SERVER["argv"][2]()->select($_SERVER["argv"][4]);
	// Netoyage des champs vides
	foreach ($racine as $k=>$i) {
		if (!$i) unset($racine[$k]);	
	}
	foreach ($toFusion as $k=>$i) {
		if (!$i) unset($toFusion[$k]);	
	}
	
	$toUpdate = array_merge($toFusion,$racine);
	ATF::$_SERVER["argv"][2]()->u($toUpdate);
	echo "Fusion des infos des deux elements\n";
	
	// On recherche les éléments associés
	$ci = ATF::db()->showConstraint($_SERVER["argv"][2]);
	foreach ($ci as $table=>$infos) {
		if (count($infos)>1) {
			
		} else {
			$infos = $infos[0];

			$c = ATF::getClass($table);
			$c->q->reset()->where($infos['key'],$toFusion['id_'.$_SERVER["argv"][2]]);
			
//			$c->q->setToString();
//			echo $c->sa()."\n";
//			$c->q->unsetToString();
			$d = $c->sa();
			foreach ($d as $el) {
				$el[$infos['key']] = $racine['id_'.$_SERVER["argv"][2]];
				$c->u($el);
			}
			echo count($d)." elements de la ".$_SERVER["argv"][2]." '".$toFusion[$_SERVER["argv"][2]]."' copie dans '".$racine[$_SERVER["argv"][2]]."'\n";
		}
	}
	
	
	ATF::db()->commit_transaction(true);
	echo "N'OUBLIEZ PAS DE SUPPRIMER LA ".strtoupper($_SERVER["argv"][2])." manuellement.\n";
} catch (errorATF $e) {
	ATF::db()->rollback_transaction(true);
	throw $e;
}
?>