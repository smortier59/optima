<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);
ATF::$usr->set('id_user',16);

echo "========= DEBUT DE SCRIPT =========\n";
createContacts();
echo "========= FIN DE SCRIPT =========\n";

function createContacts() {
    $fichier = $path == '' ? "./contact-v2.csv" : $path;
    $f = fopen($fichier, 'rb');
    $entete = fgetcsv($f);
    $lines_count = 0;
    $processed_lines = 0;

    while (($ligne = fgetcsv($f, 0, ';'))) {
        $lines_count++;

        try{
            ATF::db()->begin_transaction();

            ATF::societe()->q->reset()->where("siret", $ligne[0]);
            $societe = ATF::societe()->select_row();

            if ($societe) {
                ATF::contact()->i([
                    "id_societe" => $societe["id_societe"],
                    "civilite" => ($ligne[1] == "Monsieur" ? "M" : "Mme"),
                    "nom" => $ligne[2],
                    "prenom" => $ligne[3],
                    "fonction" => $ligne[4],
                    "email" => $ligne[5],
                    "gsm" => $ligne[6],
                    "province" => $ligne[7]
                ]);
                $processed_lines++;
            }


            ATF::db()->commit_transaction();
        } catch(errorATF $e) {
            ATF::db()->rollback_transaction();
            echo $e->getMessage()."\n";
        }
    }
    echo "Contacts créés : ".$processed_lines." Total lignes: ".$lines_count."\n";
}
