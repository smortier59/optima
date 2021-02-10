<?php
define("__BYPASS__", true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__) . "/../../global.inc.php");
ATF::define("tracabilite", false);

if(!$_SERVER["argv"][2] || ($_SERVER["argv"][2] != "list" && $_SERVER["argv"][2] != "envoi_client")){
    echo "Paramètre 2 non envoyé ou incorrect (list ou envoi_client)\n";
    return;
}
$type = $_SERVER["argv"][2];

ATF::constante()->q->reset()->where("constante", "__URL_ESPACE_CLIENT__");
$url_front_espace_client = ATF::constante()->select_row();
if(!$url_front_espace_client){
    echo "Il n'y a pas de constante __URL_ESPACE_CLIENT__ pour l'url de l'espace client\n";
    return;
}else{
    $url_front_espace_client = $url_front_espace_client["valeur"];
}
log::logger($url_front_espace_client, "mfleurquin");

ATF::constante()->q->reset()->where("constante", "__URL_ESPACE_CLIENT_BACK__");
$url_back_espace_client = ATF::constante()->select_row();
if (!$url_back_espace_client) {
    echo "Il n'y a pas de constante __URL_ESPACE_CLIENT_BACK__ pour l'url de l'espace client\n";
    return;
}else{
    $url_back_espace_client = $url_back_espace_client["valeur"];
}
log::logger($url_back_espace_client, "mfleurquin");
/*
    $application = getApplicationByDomain($url_back_espace_client, $url_front_espace_client);
    if(!$application) {
        echo "Application ID non récupérée\n";
        return;
    }else{
    $application = json_decode($application);
    if(!$application->_id){
        echo "Application ID récupéré incorrect\n";
        echo "---". $application."\n";
        return;
    }
    }

    // $mongoConnection = new MongoClient("mongodb://172.16.255.211:27017");

    echo "Application ID --> ".$app->_id."\n";
*/
echo "Tout est OK, on commence le taff \n";

//Recupere toute les affaires en cours donc on a pas encore envoyé de mail de création de compte au client
//Pour chaque affaire, on recupere le client
$q =   "SELECT commande.ref AS ref_contrat,
             commande.id_societe AS id_societe,
             societe.particulier_nom AS nom_client,
             societe.particulier_prenom AS prenom_client,
             societe.particulier_email AS email_client,
             commande.id_affaire AS id_affaire
        FROM commande
        LEFT JOIN societe ON commande.id_societe = societe.id_societe
        WHERE commande.etat != 'arreter'
        AND commande.etat != 'arreter_contentieux'
        AND commande.retour_contrat IS NOT NULL
        AND societe.date_envoi_mail_creation_compte IS NULL";



$contratsEnCours = ATF::db()->sql2array($q);
//log::logger($contratsEnCours, "mfleurquin");

$clientSansCompte = array();

foreach ($contratsEnCours as $key => $value) {

    $client = array("id_societe" => $value["id_societe"],
                    "nom" => $value["nom_client"],
                    "prenom" => $value["prenom_client"],
                    "email" => $value["email_client"],
                    "ref" => $value["ref_contrat"],
                    "affaire" => $value["id_affaire"]
                );

    //Pour chaque client, on check si on compte existe sur ECC
    //$accountExist = accountExisteEcc($url_back_espace_client, $client["email"], $application->_id );

    $clientSansCompte[] = $client;
}

//log::logger($clientSansCompte , "mfleurquin");


//Si list, on envoi la liste des client à Benjamin
if($type === "list") {
    $mail = new mail(
        array(
            "recipient" => "benjamin.tronquit@cleodis.com",
            "objet" => "Client sans compte",
            "template" => "listing_client_sans_compte",
            "clients" => $clientSansCompte
        )
    );
    $mail->send();
}
//Si envoi_client, on envoi un mail à chque client avec lien pour créer son compte
if($type === "envoi_client") {
    $i=0;
    foreach ($clientSansCompte as $k => $client) {
        if($i < 2){
            ATF::societe()->demande_creation_compte_espace_client($client, $url_front_espace_client);
            $i++;
        }
    }
}


/*
function getApplicationByDomain($url_back_espace_client, $url_front_espace_client) {
    $curl = curl_init();
    $host = str_replace("http://", "", $url_back_espace_client);
    $host = str_replace("https://", "", $host);

    echo "Récupération de l'application ID, appel CURL : ".$url_back_espace_client . '/application/domain?domain=' . $url_front_espace_client."\n";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_back_espace_client.'/application/domain?domain=' . $url_front_espace_client,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'host: '. $host
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

function accountExisteEcc($url_back_espace_client, $ecmailClient, $applicationId) {
    $curl = curl_init();

    echo "Check si le client à un compte : " . $url_back_espace_client . '/account/existAccountForOptima?email=' . $ecmailClient . '&applicationId=' . $applicationId."\n";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url_back_espace_client.'/account/existAccountForOptima?email='. $ecmailClient .'&applicationId='. $applicationId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: text/plain'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}*/