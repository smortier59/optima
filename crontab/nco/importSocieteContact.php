<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "nco";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::db()->begin_transaction();

$fichierSoc = "societes.csv";
$fic = fopen($fichierSoc, 'rb');

$entete = fgetcsv($fic);
ATF::societe()->insertWithId = true;

$ct=0;
while ($ligne = fgetcsv($fic)) {
	$ct++;
	$toInsert = array(
		"id_societe"=>$ligne[0],
		"date"=>$ligne[1],
		"nom_commercial"=>$ligne[4],
		"ref"=>$ligne[5],
		"id_pays"=>"FR",
		"id_famille"=>$ligne[7],
		"siren"=>$ligne[8],
		"siret"=>$ligne[9],
		"naf"=>$ligne[10],
		"societe"=>$ligne[11],
		"nom_commercial"=>$ligne[12],
		"adresse"=>$ligne[13],
		"adresse_2"=>$ligne[14],
		"adresse_3"=>$ligne[15],
		"cp"=>$ligne[16],
		"ville"=>$ligne[17],
		"id_contact_facturation"=>$ligne[18],
		"facturation_id_pays"=>$ligne[19],
		"facturation_adresse"=>$ligne[20],
		"facturation_adresse_2"=>$ligne[21],
		"facturation_adresse_3"=>$ligne[22],
		"facturation_cp"=>$ligne[23],
		"facturation_ville"=>$ligne[24],
		"reference_tva"=>$ligne[25],
		"iban"=>$ligne[26],
		"latitude"=>$ligne[27],
		"longitude"=>$ligne[28],
		"tel"=>$ligne[29],
		"fax"=>$ligne[30],
		"email"=>$ligne[31],
		"web"=>$ligne[32],
		"activite"=>$ligne[33],
		"etat"=>$ligne[34],
		"nb_employe"=>$ligne[35],
		"effectif"=>$ligne[36],
		"id_secteur_geographique"=>$ligne[37],
		"id_contact_commercial"=>$ligne[38],
		"liens"=>$ligne[39],
		"ca"=>$ligne[40],
		"structure"=>$ligne[42],
		"capital"=>$ligne[43],
		"date_creation"=>$ligne[44],
		"id_filiale"=>$ligne[45],
		"notes"=>$ligne[46],
		"fournisseur"=>$ligne[47],
		"partenaire"=>$ligne[48],
		"delai_relance"=>$ligne[49],
		"code_fournisseur"=>$ligne[50],
		"divers_1"=>$ligne[51],
		"relation"=>$ligne[52],
		"rib"=>$ligne[53],
		"banque"=>$ligne[54],
		"bic"=>$ligne[55],
		"swift"=>$ligne[56],
		"rib_affacturage"=>$ligne[57],
		"iban_affacturage"=>$ligne[58],
		"bic_affacturage"=>$ligne[59],
		"id_region"=>$ligne[61],
		"insee"=>$ligne[62],
		"canton"=>$ligne[63],
		"id_owner"=>$ligne[64]

	);



	try {
		$id_societe = ATF::societe()->i($toInsert);
		echo "Societe : ".$toInsert['societe']." CREATE\n";
	} catch (errorATF $e) {
		echo "Societe : ".$toInsert['societe']." ERREUR\n";
		$ct++;
    	//ATF::db()->rollback_transaction();
		$errorsS[] = "LIGNE dans le CSV n°".$ct." - Code erreur : ".$e->getErrno()." => ".$e->getMessage();
		//throw $e;
	}
	unset($u);
}


$fichierCon = "contacts.csv";
$fic = fopen($fichierCon, 'rb');

$entete = fgetcsv($fic);

$ct=0;
while ($ligne = fgetcsv($fic)) {
	$ct++;
	if (!$ligne[2] || !$ligne[5]) continue;

	if (!ATF::societe()->select($ligne[5])) {
		$errorsC[] = "LIGNE dans le CSV n°".$ct." - Code erreur : XX => Société inexistante";
		continue;
	}
	$toInsert = array(
	    "civilite"=>$ligne[1],
	    "nom"=>$ligne[2],
	    "prenom"=>$ligne[3],
	    "etat"=>$ligne[4],
	    "id_societe"=>$ligne[5],
	    "adresse"=>$ligne[6],
	    "adresse_2"=>$ligne[7],
	    "adresse_3"=>$ligne[8],
	    "cp"=>$ligne[9],
	    "ville"=>$ligne[10],
	    "id_pays"=>"FR",
	    "tel"=>$ligne[12],
	    "gsm"=>$ligne[13],
	    "fax"=>$ligne[14],
	    "email"=>$ligne[15],
	    "fonction"=>$ligne[16],
	    "departement"=>$ligne[17],
	    "anniversaire"=>$ligne[18],
	    "loisir"=>$ligne[19],
	    "langue"=>$ligne[20],
	    "assistant"=>$ligne[21],
	    "assistant_tel"=>$ligne[22],
	    "tel_autres"=>$ligne[23],
	    "adresse_autres"=>$ligne[24],
	    "forecast"=>$ligne[25],
	    "description"=>$ligne[26]
	);

	try {
		ATF::contact()->i($toInsert);
		echo "contact : ".$toInsert['contact']." CREATE\n";
	} catch (errorATF $e) {
		echo "contact : ".$toInsert['contact']." ERREUR\n";
		$ct++;
		$errorsC[] = "LIGNE dans le CSV n°".$ct." - Code erreur : ".$e->getErrno()." => ".$e->getMessage();
    	//ATF::db()->rollback_transaction();
		//throw $e;
	}

}
print_r($errorsS);
print_r($errorsC);
ATF::db()->commit_transaction();
