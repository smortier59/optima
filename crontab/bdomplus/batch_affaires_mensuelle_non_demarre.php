<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$log_file = "bdomplus/controle_affaire_mensuelle".date("Ymd");


log::logger("============================================================", $log_file);
log::logger("======  Début du script par le controle des affaires mensuelles  ======", $log_file);
log::logger("============================================================", $log_file);


try{
    ATF::db()->begin_transaction();

    ATF::affaire()->q->reset()
        ->whereIsNull("id_magasin","AND", "affaire")
        ->where("affaire.etat","commande","AND")
        ->where("affaire.site_associe","bdomplus","AND");
    $affaireshier = ATF::affaire()->select_all();

    if ($affaireshier) {
        foreach ($affaireshier as $key => $value) {

            ATF::suivi()->q->reset()->where("texte", "Retour Order SLIMPAY :%", "AND", false, "LIKE")
                                ->where("id_affaire", $value["affaire.id_affaire"]);
            $suivi = ATF::suivi()->sa();

            if ($suivi[0]) {
                $order = $suivi[0]['texte'];
                $order = str_replace('Retour Order SLIMPAY : ', '', $order);
                $order = json_decode($order, true);

                ATF::affaire()->q->reset()
                    ->addAllFields("affaire")
                    ->where("affaire.id_affaire", $value["affaire.id_affaire"]);
                $affaire = ATF::affaire()->select_row();

                log::logger($affaire["affaire.ref"] , $log_file);

                ATF::souscription()->controle_affaire($affaire, $order);
            }
        }

    } else {
        log::logger("================================== Aucunne affaire mensuelle non démarrée  =====================", $log_file);
    }

    ATF::db()->commit_transaction();

} catch (errorATF $e) {
    ATF::db()->rollback_transaction();
    print_r($e);
}
log::logger("================================== Fin du script  =====================", $log_file);
