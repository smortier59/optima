#!/usr/bin/php
<?php
/** Script d'insertion de logs en Base
* Insere les logs :
*			erreurs du programme (erreurs fatales, exceptions)
*			insertion en base
*			modification (update) en base
*			suppression (delete) en base
* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
*/

include("../global.inc.php");
try{
	ATF::log()->insert_error_log();
	echo "[ATF - Gestion des erreurs] Fichier Log erreurs ajoute en Base\n";
	ATF::log()->insert_insert_log();
	echo "[ATF - Gestion des erreurs] Fichier Log insertion ajoute en Base\n";
	ATF::log()->insert_update_log();
	echo "[ATF - Gestion des erreurs] Fichier Log update ajoute en Base\n";
	ATF::log()->insert_delete_log();
	echo "[ATF - Gestion des erreurs] Fichier Log delete ajoute en Base\n";
}catch(errorATF $e){
	$e->setError();
	echo "[ATF - Gestion des erreurs] ERREUR DU SCRIPT D'AJOUT DE LOG EN BASE\n";
	//Affichage des erreurs
	$tab=ATF::$msg->getAll();print_r($tab);
}
?>
