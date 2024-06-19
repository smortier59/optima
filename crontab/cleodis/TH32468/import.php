<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);
ATF::$usr->set('id_user',16);

if (!isset($_SERVER["argv"][2])) {
    echo "ID Apporteur manquant\n";
} else {
    $id_apporteur = $_SERVER["argv"][2];

    $sirens=[
        "382742013" => "38274201301501",
        "392640090" => "39264009003754",
        "384402871" => "38440287100543",
        "382900942" => "38290094200014",
        "383451267" => "38345126700017",
        "383952470" => "38395247001746",
        "383686839" => "38368683901836",
        "383354594" => "38335459402349",
        "383000692" => "38300069204998",
        "384353413" => "38435341302002",
        "775559404" => "77555940400014",
        "379155369" => "37915536900133",
        "605520071" => "60552007102384",
        "755501590" => "75550159001407",
        "857500227" => "85750022702672",
        "542820352" => "54282035201283",
        "058801481" => "05880148101264",
        "457506566" => "45750656600340",
        "560801300" => "56080130000990",
        "554200808" => "55420080800018",
        "549800373" => "54980037300926",
        "542104245" => "54210424501419",
        "349974931" => "34997493101213",
        "352483341" => "35248334100017",
        "775618622" => "77561862205126",
        "384006029" => "38400602904193",
        "356801571" => "35680157100015",
        "552002313" => "55200231303603",
        "552091795" => "55209179500492",
        "353821028" => "35382102805018"
    ];

    echo "========= DEBUT DE SCRIPT =========\n";
    createSocietes($id_apporteur, $sirens);
    createContacts($sirens);
    echo "========= FIN DE SCRIPT =========\n";

}


function createSocietes($id_apporteur, &$sirens) {
    $fichier = $path == '' ? "./societes.csv" : $path;
    $f = fopen($fichier, 'rb');
    $entete = fgetcsv($f);
    $lines_count = 0;
    $processed_lines = 0;
    $doublons = 0;

    while (($ligne = fgetcsv($f, 0, ';'))) {
        $lines_count++;
        try{
            ATF::db()->begin_transaction();

            if ($sirens[$ligne[0]]) {
                $siret = $sirens[$ligne[0]];
                $id_societe = findSociete($siret);
                if (!$id_societe) {
                    $data = ATF::creditsafe()->getInfosCompanyBySiret($ligne[0]);
                }
            } else {
                echo $ligne[0]." pas dans sirens \n";
                $data = ATF::creditsafe()->getInfosCompanyBySiret($ligne[0]);
                $siret = $data["siret"];
                echo $siret."\n";
                $id_societe = findSociete($siret);
            }

            if ($id_societe) {
                ATF::societe()->u(["id_societe"=> $id_societe, "province" => $ligne[2]]);
                $doublons++;
            } else {
                $data["province"] = $ligne[2];
                unset($data["nb_employe"],$data["resultat_exploitation"],$data["capitaux_propres"],$data["dettes_financieres"],$data["capital_social"], $data["gerant"]);

                $data["id_apporteur"] = $id_apporteur;
                ATF::societe()->insert(array("societe"=>$data));
                $processed_lines++;
            }
            $sirens[$ligne[0]] = $siret;

            ATF::db()->commit_transaction();
        } catch(errorATF $e) {
            ATF::db()->rollback_transaction();
            echo $e->getMessage()."\n";
        }
    }
    echo "Sociétés créées : ".$processed_lines." Doublons :".$doublons." Total lignes: ".$lines_count."\n";
}

function createContacts($sirens) {
    $fichier = $path == '' ? "./contacts.csv" : $path;
    $f = fopen($fichier, 'rb');
    $entete = fgetcsv($f);
    $lines_count = 0;
    $processed_lines = 0;

    while (($ligne = fgetcsv($f, 0, ';'))) {
        $lines_count++;

        try{
            ATF::db()->begin_transaction();

            $societe = findSociete($sirens[$ligne[2]]);
            if ($societe) {
                ATF::contact()->i([
                    "id_societe" => $societe,
                    "civilite" => ($ligne[5] == "Monsieur" ? "M" : "Mme"),
                    "nom" => $ligne[6],
                    "prenom" => $ligne[7],
                    "fonction" => $ligne[8],
                    "email" => $ligne[9],
                    "gsm" => $ligne[10]
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

function findSociete($siret) {
    ATF::societe()->q->reset()->where("siret", ATF::db()->real_escape_string($siret));
    $res = ATF::societe()->sa();
    if (count($res) > 0) {
        return $res[0]["id_societe"];
    }
    return null;
}