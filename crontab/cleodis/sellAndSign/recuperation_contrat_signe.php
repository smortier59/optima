<?php
define("__BYPASS__",true);
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);

$codename = $_SERVER["argv"][1];

$log_file = "recuperation-contrat-signe-".$codename;

log::logger('Starting cronjobs - Récupération du contrat signé Sell&Sign - ' . date('Y-m-d H:i:s'), $log_file);

log::logger('Récuperation des données sell_and_sign', $log_file);


$q = "SELECT *
      FROM sell_and_sign
      WHERE contrat_signe = 'absent'
      AND (etat IS NULL OR etat != 'ABANDONED' AND etat != 'UNKNOWN')
      ORDER BY date DESC
      LIMIT 0, 200";

$data = ATF::db()->sql2array($q);

if ($data) {
    log::logger('Nombre de Données sell_and_sign existantes: ' . count($data), $log_file);
    foreach ($data as $sellAndSignData) {
        ATF::db()->begin_transaction();
        try {
            log::logger("-------------------------------------------------------", $log_file);
            $id_affaire = $sellAndSignData["id_affaire"];

            ATF::commande()->q->reset()->where("commande.id_affaire", $id_affaire)->setLimit(1);
            $contrat = ATF::commande()->select_row();

            if ($contrat) {
                $id_commande = $contrat["commande.id_commande"];
                log::logger("Traitement de l'enregistrement sell and sign ID: ". $sellAndSignData["id_sell_and_sign"] ." / Contract ID : ". $sellAndSignData["contract_id"] ." / Affaire ID ". $sellAndSignData["id_affaire"] , $log_file);
                ATF::sell_and_sign()->u(array("id_sell_and_sign" => $sellAndSignData["id_sell_and_sign"], "etat" => "UNKNOWN"));

                try {
                    $infos = ATF::sell_and_sign()->getContract($sellAndSignData["contract_id"]);
                    log::logger($infos, $log_file);

                    if (property_exists($infos, 'id')) {
                        log::logger("Statut du contract " . $infos->status , $log_file);
                        ATF::sell_and_sign()->u(array("id_sell_and_sign" =>  $sellAndSignData["id_sell_and_sign"], "etat" => $infos->contract->status));

                        if ($infos->status == 'ABANDONED') {
                            // ATF::sell_and_sign()->u(array("id_sell_and_sign" =>  $sellAndSignData["id_sell_and_sign"], "statut" => 'abondonne'));
                        } elseif ($infos->id && ($infos->status == 'ARCHIVED' || $infos->status == 'CLOSED')) {
                            log::logger("Contrat archivé, on recupere le contrat signé" , $log_file);
                            $res = ATF::sell_and_sign()->getSignedContract($infos->id, $id_commande);

                            if ($res !== false) {
                                log::logger("PDF récupéré chez Sell and Sign, on le stock", $log_file);
                                util::file_put_contents(ATF::commande()->filepath($id_commande, 'retour', null, $codename), $res);
                                ATF::sell_and_sign()->u(array("id_sell_and_sign" =>  $sellAndSignData["id_sell_and_sign"], "contrat_signe" => "present"));

                                log::logger("Update de la date de retour contrat sur la commande ", $log_file);
                                ATF::commande()->updateDate(array("id_commande" =>  $id_commande, "retour_contrat" => date("Y-m-d")));
                            } else {
                                log::logger("PDF non récupéré chez Sell and Sign", $log_file);
                            }
                        } else {
                            log::logger("Statut non pris en charge " . $infos->status , $log_file);
                        }
                    } else {
                        log::logger("Contrat introuvable chez Sell and Sign", $log_file);
                    }
                } catch (errorATF $e) {
                    ATF::db()->rollback_transaction();
                }
            } else {
                log::logger("Pas de commande associée", $log_file);
            }
            ATF::db()->commit_transaction();
        } catch (errorATF $e) {
            log::logger($e->getMessage());
            ATF::db()->rollback_transaction();
        }
    }
} else {
    log::logger('Aucun enregistrement a traiter', $log_file);
}

log::logger("On met à jour toute les affaires dont les contrats signés ont été récupéré" , $log_file);

$q = "UPDATE sell_and_sign SET contrat_signe='traite_autre_ligne' WHERE id_affaire IN (SELECT id_affaire FROM sell_and_sign WHERE contrat_signe = 'present') AND contrat_signe = 'absent'";
ATF::db()->sql2array($q);


log::logger("On passe en ABANDONED les lignes S&Sign de plus de 3 mois dont le contrat est toujours absent" , $log_file);
$q = "UPDATE sell_and_sign SET etat='ABANDONED' WHERE contrat_signe='absent' AND date < '".date("Y-m-d 00:00:00", strtotime("+ 3 months"))."'";
ATF::db()->sql2array($q);

log::logger('Ending cronjobs - Récupération du contrat signé Sell&Sign - ' . date('Y-m-d H:i:s'), $log_file);


/*
ALTER TABLE sell_and_sign CHANGE contrat_signe contrat_signe ENUM('present','absent','traite_autre_ligne') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'absent';

*/
?>