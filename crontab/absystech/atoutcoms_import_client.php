<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "atoutcoms";
include(dirname(__FILE__)."/../../global.inc.php");
log::logger("------------- DEBUT DE SCRIPT -------------", $logFile);

$logFile = "atoutcoms_import_client";
echo "Logfile : ".__ABSOLUTE_PATH__."/log/".$logFile."\n";

$societe_inserees = 0;
$contact_inseres = 0;
/*
	MAPPING
    [0] => Societe
    [1] => Adresse1
    [2] => Adresse2
    [3] => Code_postal
    [4] => Ville
    [5] => Cedex
    [6] => Telephone
    [7] => Fax
    [8] => Activite
    [9] => Num_SIRET
    [10] => CLI_CONTACTS::Civilite
    [11] => CLI_CONTACTS::Nom
    [12] => CLI_CONTACTS::Prenom
    [13] => CLI_CONTACTS::Mail
    [14] => CLI_CONTACTS::Telephone
    [15] => CLI_CONTACTS::Portable



 */

ATF::db()->begin_transaction(true);

try {
	$q = "SELECT * FROM `TABLE 156`";
	$soc_contacts = ATF::db()->sql2array($q);

	$entetes = array_shift($soc_contacts);

	foreach ($soc_contacts as $k=>$data) {
		log::logger("Traitement de la ligne n°".$k, $logFile);
		$data = array_values($data);
		

		$societe = array();
		$id_societe = null;
		if ($data[0]) {
			$societe['societe'] = $data[0];
			$societe['adresse'] = $data[1];
			$societe['adresse_2'] = $data[2];
			$societe['cp'] = $data[3];
			$societe['ville'] = $data[4];
			$societe['adresse_3'] = $data[5];
			$societe['tel'] = $data[6];
			$societe['fax'] = $data[7];
			$societe['activite'] = $data[8];
			$societe['siret'] = $data[9];
			
			log::logger("Société ", $logFile);
			log::logger($societe, $logFile);
			try {
				$id_societe = ATF::societe()->i($societe);
				log::logger("Insert done ID : ".$id_societe, $logFile);

				$societe_inserees++;
			} catch (errorATF $e) {
				// log::logger($e->getCode(), $logFile);
				// log::logger($e->getMessage(), $logFile);
				if ($e->getCode() != 1011) throw $e;
			}

		}

		$contact = array();
		$id_contact = null;
		if ($data[11] && $id_societe) {
			switch ($data[10]) {
				case "Madame": 
				case "mme":
					$civilite = "Mme";
				case "Mademoiselle":
					$civilite = "Mlle";
				default:
					$civilite = "M";

			}

			// Traitement email de merde
			if ($data[13] == "securitestblériot@hotmail.com") $data[13] = "securitestbleriot@hotmail.com";
			if ($data[13] == "agencedu centre9@wanadoo.fr") $data[13] = "agenceducentre9@wanadoo.fr";

			$contact['civilite'] = $civilite;
			$contact['nom'] = $data[11];
			$contact['prenom'] = $data[12];
			$contact['email'] = $data[13];
			$contact['tel'] = $data[14];
			$contact['gsm'] = $data[15];
			$contact['id_societe'] = $id_societe;
			log::logger("Contact ", $logFile);
			log::logger($contact, $logFile);
			$id_contact = ATF::contact()->i($contact);
			log::logger("Insert done ID : ".$id_contact, $logFile);
			$contact_inseres++;
		}
	}
} catch (errorATF $e) {
	ATF::db()->rollback_transaction();
	throw $e;
}

log::logger("------------- FIN DE SCRIPT -------------", $logFile);
log::logger("Sociétés insérées : ".$societe_inserees, $logFile);
log::logger("Contacts insérées : ".$contact_inseres, $logFile);
// ATF::db()->rollback_transaction();	
ATF::commit_transaction(true);	