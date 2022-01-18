<?php
define("__BYPASS__", true);
include(dirname(__FILE__) . "/../../global.inc.php");
ATF::define("tracabilite", false);

if(!$_SERVER["argv"][1] ||
 ($_SERVER["argv"][1] !== "cleodis" &&  $_SERVER["argv"][1] !== "cleodisbe" &&  $_SERVER["argv"][1] !== "assets" &&  $_SERVER["argv"][1] !== "bdomplus")){
	echo "Paramètre 1 non envoyé ou incorrect (cleodis / cleodisbe / assets / bdomplus)\n";
	return;
}


if(!$_SERVER["argv"][2] || ($_SERVER["argv"][2] != "list" && $_SERVER["argv"][2] != "envoi_client")){
	echo "Paramètre 2 non envoyé ou incorrect (list ou envoi_client)\n";
	return;
}
$type = $_SERVER["argv"][2];

try{
	$url_back_espace_client = ATF::espace_client_conseiller()->getUrlBack();
	$url_front_espace_client = ATF::espace_client_conseiller()->getUrlFront();
}catch(errorATF $e){
	echo "Une erreur est survenue : \n\n";
	echo $e->getMessage();
}

if($url_back_espace_client &&  $url_front_espace_client){
	$application = ATF::espace_client_conseiller()->getApplicationByDomain($url_back_espace_client, $url_front_espace_client);
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

	echo "Application ID --> ".$application->_id."\n";
	echo "Tout est OK, on commence le taff \n";

	//Recupere toute les affaires en cours donc on a pas encore envoyé de mail de création de compte au client
	//Pour chaque affaire, on recupere le client
	$q =   "SELECT DISTINCT(commande.id_societe) AS id_societe,
				commande.ref AS ref_contrat,
				societe.id_societe AS id_societe,
				commande.id_affaire AS id_affaire
			FROM commande
			LEFT JOIN societe ON commande.id_societe = societe.id_societe
			WHERE commande.etat != 'arreter'
			AND commande.etat != 'arreter_contentieux'
			AND commande.retour_contrat IS NOT NULL
			AND societe.date_envoi_mail_creation_compte IS NULL";



	$contratsEnCours = ATF::db()->sql2array($q);

	$clientSansCompte = array();
	$clients = array();
	$i = 0;

	foreach ($contratsEnCours as $key => $value) {
		if (ATF::societe()->select($value["id_societe"], "id_famille") === 9) {
			$dataSociete = ATF::societe()->select($value["id_societe"]);
			$client = array("id_societe" => $value["id_societe"],
							"nom" => $dataSociete["particulier_nom"],
							"prenom" => $dataSociete["particulier_prenom"],
							"email" => $dataSociete["particulier_email"],
							"ref" => $value["ref_contrat"],
							"affaire" => $value["id_affaire"]
						);

			$clients[] = $client;
		} else {
			$dataSociete = ATF::societe()->select($value["id_societe"]);
			if ($dataSociete["id_contact_signataire"]) {
				$signataire = ATF::contact()->select($dataSociete["id_contact_signataire"]);
				if ($signataire["email"]) {
					$client = array("id_societe" => $value["id_societe"],
							"nom" => $signataire["nom"],
							"prenom" => $signataire["prenom"],
							"email" => $signataire["email"],
							"ref" => $value["ref_contrat"],
							"affaire" => $value["id_affaire"]
						);
					$clients[] = $client;
				}
			}
		}
	}
	$clients = ATF::espace_client_conseiller()->checkAccountsExiste($url_back_espace_client, $application->_id,  $clients );
	$clients = json_decode($clients, true);
	//log::logger($clients , "mfleurquin");

	foreach ($clients["clients"] as $key => $value) {
	   if(!$value["existe"]){
		$clientSansCompte[] = $value;
	   } else {
		log::logger("Client existant -->", "mfleurquin");
		log::logger($value, "mfleurquin");
	   }
	}


	//Si list, on envoi la liste des client à Benjamin
	if($type === "list") {
		echo "Envoi du mail contenant la liste des clients sans compte à benjamin.tronquit@cleodis.com\n";
		if ($_SERVER["argv"][1] === "bdomplus") {
			ATF::societe()->q->reset()->where("siret", "52933929300043");
			$partenaire = ATF::societe()->select_row();
		} else {
			ATF::societe()->q->reset()->where("siret", "45307981600055");
			$partenaire = ATF::societe()->select_row();
		}

		$mail = new mail(
			array(
				"recipient" => "benjamin.tronquit@cleodis.com",
				"objet" => "Client sans compte",
				"template" => "listing_client_sans_compte",
				"clients" => $clientSansCompte,
				"colors" => array(
					"dominant" => "#FD5300",
					"footer" => "#161C5F",
					"links" => "#161C5F",
					"titles" => "#161C5F"
				),
				"partenaire"=> $partenaire
			)
		);
		$mail->send();
	}
	//Si envoi_client, on envoi un mail à chque client avec lien pour créer son compte
	if($type === "envoi_client") {

		foreach ($clientSansCompte as $k => $client) {
			echo "Envoi du mail contenant la demande de création de compte à ".$client["email"]."\n";
			try{
				ATF::societe()->demande_creation_compte_espace_client($client, $url_front_espace_client);
				ATF::suivi()->insert(array(
					"id_societe"=>$client["id_societe"],
					"type"=>"email",
					"texte"=>"Envoi du mail de création de compte client à ".$client["email"],
				));
			} catch(errorATF $e){
				echo $e->getMessage()."\n";
			}
		}
	}
}





