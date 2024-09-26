<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);
ATF::$usr->set('id_user',16);

$url = $arv[4];
$applicationId = $argv[3];

echo "========= DEBUT DE SCRIPT =========\n";
// createSocietes();
// createContacts();
creationCompteEspacePartenaire($url, $applicationId);
echo "========= FIN DE SCRIPT =========\n";


$terence = 93;
$faustine = 137;
$apporteurSociete = 30229;
$apporteurContact = 59607;


function createSocietes() {
    $fichier = $path == '' ? "./societes.csv" : $path;
    $f = fopen($fichier, 'rb');
    $entete = fgetcsv($f, 0, ';');
    $lines_count = 0;
    $processed_lines = 0;
    $doublons = 0;


    while (($ligne = fgetcsv($f, 0, ';'))) {
        $lines_count++;
        try{
            ATF::db()->begin_transaction();
            $id_societe =null;

            $societe = [
                "id_owner" => 93,
                "id_assistante" => 137,
                "siret" => $ligne[2],
                "code_client" => $ligne[3],
                "code_fournisseur" => $ligne[4],
                "id_apporteur" => 30229,
                "id_prospection" => 59607,
                "societe"=> $ligne[7],
                "nom_commercial"=> $ligne[8],
                "adresse"=> $ligne[9],
                "cp"=> str_replace(" ", "", $ligne[10]),
                "ville"=> $ligne[11],
                "tel" => $ligne[12],
                "email" => str_replace(" ", "", str_replace(",", ".", $ligne[13])),
                "partenaire" => $ligne[14],
                "fournisseur" => $ligne[15],
                "IBAN" => $ligne[16],
                "BIC" => $ligne[17]
            ];

            if ($societe["siret"]) {
                $id_societe = findSociete($societe["siret"]);
            }

            if ($id_societe) {
                $societe["id_societe"] = $id_societe;
                ATF::societe()->u($societe);
                $doublons++;
            } else {
                ATF::societe()->insert(array("societe"=>$societe));
                $processed_lines++;
            }
            ATF::db()->commit_transaction();
        } catch(errorATF $e) {
            log::logger($societe, "mfleurquin");
            ATF::db()->rollback_transaction();
            echo $e->getMessage()."\n";
        }
    }
    echo "Sociétés créées : ".$processed_lines." Doublons :".$doublons." Total lignes: ".$lines_count."\n";
}

function createContacts() {
    $fichier = $path == '' ? "./contacts.csv" : $path;
    $f = fopen($fichier, 'rb');
    $entete = fgetcsv($f, 0, ';');
    $lines_count = 0;
    $processed_lines = 0;
    $doublons = 0;


    while (($ligne = fgetcsv($f, 0, ';'))) {
        $lines_count++;
        try{
            ATF::db()->begin_transaction();

            if ($ligne[0]) {
                $id_societe = findSociete($ligne[0]);
            }

            if ($id_societe) {
                $contact = [
                    "id_societe" => $id_societe,
                    "nom" => $ligne[1],
                    "prenom" => $ligne[2],
                    "fonction" => $ligne[3],
                    "email" => str_replace(" ", "", str_replace(",", ".", $ligne[5])),
                    "tel" => $ligne[4]
                ];
                ATF::contact()->i($contact);
                $processed_lines++;
            } else {
                echo "SIRET non trouvé : ".$ligne[0]."\n";

            }
            ATF::db()->commit_transaction();
        } catch(errorATF $e) {
            log::logger($contact, "mfleurquin");
            ATF::db()->rollback_transaction();
            echo $e->getMessage()."\n";
        }
    }
    echo "Contacts créés : ".$processed_lines." Total lignes: ".$lines_count."\n";
}

function creationCompteEspacePartenaire($url, $applicationId) {
    $fichier = $path == '' ? "./contactEspacePartenaire.csv" : $path;
    $f = fopen($fichier, 'rb');
    $entete = fgetcsv($f, 0, ';');
    $lines_count = 0;
    $processed_lines = 0;
    $doublons = 0;


    while (($ligne = fgetcsv($f, 0, ';'))) {
        $lines_count++;
        try{
            if ($ligne[0] && $ligne[0]) {
                $id_societe = findSociete($ligne[0]);
            }

            if ($id_societe) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://".$url."/account/importAccount");
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, '');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_ENCODING, '');
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

                $data = [
                    "applicationId" => $applicationId,
                    "permissions"  => [
                        "business.view",
                        "optimaApi.public",
                        "contract.view",
                        "payments.list",
                        "partenaire-demande.view",
                        "partenaire-demande.create",
                        "meeloInfos.view",
                        "creditSafeInfos.view",
                        "contrat.view",
                        "parc.view",
                        "suivis.view",
                        "home.partenaire",
                        "business.view",
                        "optimaApi.public",
                        "payments.list"
                    ],
                    "email" => str_replace(" ", "", str_replace(",", ".", $ligne[5])),
                    "nom" => $ligne[1],
                    "prenom" => $ligne[2],
                    "idSocietes" => $id_societe,
                    "password" => $ligne[6]
                ];

                print_r($data);
                $data_string = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string );

                $response = curl_exec($ch);
                $response = json_decode($response);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_status === 200) {
                    $processed_lines++;
                }else{
                    echo $response->message;
                }

            } else {
                echo "SIRET non trouvé : ".$ligne[0]."\n";
            }
        } catch(errorATF $e) {
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