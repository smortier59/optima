<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "nco";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::db()->begin_transaction();

$fichierSoc = "prospects.csv";
$fic = fopen($fichierSoc, 'rb');

$entete = fgetcsv($fic);


$ct=0;
while ($ligne = fgetcsv($fic)) {

	// ID COMMERCIAL
	if ($ligne[0]=="AED") { // Antoine
		$id_owner = 1;
	} else if ($ligne[0]=="AER") { // Alexandre
		$id_owner = 14;
	} else if ($ligne[0]=="CET") { // Caroline
		$id_owner = 16;
	} else if ($ligne[0]=="FEV") { // Francine
		$id_owner = 18;
	} else { 
		$id_owner = NULL;
	}

	// RELATION
	if ($ligne[2]=="CLI") {
		ATF::societe()->q->where("siret",$ligne[13])->where("siren",$ligne[12]);
		if (ATF::societe()->select_row()) continue;
	}

	// Effectif et tranche effectif
	if ($ligne[19]<10) {
		$eff = 1;
	} else if ($ligne[19]<50) {
		$eff = 10;
	} else if ($ligne[19]<100) {
		$eff = 50;
	} else if ($ligne[19]<500) {
		$eff = 100;
	} else if ($ligne[19]<1000) {
		$eff = 500;
	} else if ($ligne[19]>=1000) {
		$eff = 1000;
	}

	$ct++;
	$soc = array(
		"id_owner"=>$id_owner,
		"societe"=>$ligne[1],
		"relation"=>$ligne[2]?"C":"P",
		"cp"=>$ligne[3],
		"ville"=>$ligne[4],
		"id_secteur_geographique"=>$ligne[5],
		"tel"=>$ligne[6]?"0".$ligne[6]:"",
		"siren"=>$ligne[12],
		"siret"=>$ligne[13],
		"adresse"=>$ligne[14],
		"naf"=>$ligne[15],
		"activite"=>$ligne[16],
		"fax"=>$ligne[17]?"0".$ligne[17]:"",
		"ca"=>$ligne[18],
		"nb_employe"=>$ligne[19],
		"effectif"=>$eff,
		"date_creation"=>$ligne[20],
		"structure"=>$ligne[21],
		"capital"=>$ligne[22]
	);





	unset($id_soc);
	try {
		$id_soc = ATF::societe()->i($soc);
		echo "Societe : ".$soc['societe']." CREATE\n";
	} catch (error $e) {
		echo "Societe : ".$soc['societe']." ERREUR\n";
		$ct++;
    	//ATF::db()->rollback_transaction();
		$errorsS[] = "LIGNE dans le CSV n°".$ct." - Code erreur : ".$e->getErrno()." => ".$e->getMessage();
		//throw $e;
	}

	if ($id_soc) {
		// contact 
		$nom = $ligne[11]?$ligne[11]:$ligne[8];
		$contact = array(
			"fonction"=>$ligne[7],
			"nom"=>$nom?$nom:"X",
			"civilite"=>strtolower($ligne[9]),
			"prenom"=>$ligne[10],
			"id_societe"=>$id_soc
		);


		try {
			$id_con = ATF::contact()->i($contact);
			echo "Contact : ".$contact['nom']." CREATE\n";
		} catch (error $e) {
			echo "Contact : ".$contact['nom']." ERREUR\n";
			$ct++;
	    	//ATF::db()->rollback_transaction();
			$errorsS[] = "LIGNE dans le CSV n°".$ct." - Code erreur : ".$e->getErrno()." => ".$e->getMessage();
			//throw $e;
		}

	}





}

print_r($errorsS);

ATF::db()->commit_transaction();
