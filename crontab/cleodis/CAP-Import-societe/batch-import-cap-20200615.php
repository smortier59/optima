
<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cap";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);

echo "========= DEBUT DE SCRIPT =========\n";

// Début de transaction SQL
ATF::db()->begin_transaction();


$log_import = "log_import_update";
$log_erreur = "log_erreur_update";


/*$toDelete = array(12825 ,8205, 12810, 12332, 12046, 12837, 12034, 12579, 11897, 11865, 12349, 12600, 12926);

foreach ($toDelete as $key => $value) {
	$q = "DELETE FROM `societe` WHERE `societe`.`id_societe` = ".$value;
	ATF::db()->sql2array($q);
}*/

// If path is not supplied, then get default path
$fileSociete = $path == '' ? "./cap-20200615.csv" : $path;
$fpr = fopen($fileSociete, 'rb');
$entete = fgetcsv($fpr, 0, ";");
$societes = $campagnes = array();


$err = 0;
$lines_count = 0;
$processed_lines = 0;

$users = $contacts = $devises = array();

while ($ligne = fgetcsv($fpr, 0, ';')) {

	$lines_count++;

	//log::logger($ligne , $log_import);

	if (!$ligne[0]) continue; // pas d'ID pas de chocolat

	$data = array();
	foreach ($ligne as $key => $value) {

		if($value){
			switch ($entete[$key]) {

				case 'id_societe':
					$data[$entete[$key]] = $value;
				break;


				case 'id_fournisseur':
				case 'id_apporteur':
					ATF::societe()->q->reset()->where("societe", $value, "OR");
					if($societe = ATF::societe()->select_row()){
						$data[$entete[$key]] = $societe["id_societe"];
					}else{
						throw new errorATF($entete[$key]." ".$value." non trouvée");
					}
				break;

				case 'id_owner':
				case 'id_assistante':
					if($value !== " "){
						if(!$users[$value]){
							$user = explode(" ", $value);

							ATF::user()->q->reset()->where("prenom", $user[0])->where("nom", $user[1]);
							if($user = ATF::user()->select_row()){
								$users[$value] = $user["id_user"];
							}else{
								throw new errorATF($entete[$key]." ".$value." non trouvé");
							}
						}
						$data[$entete[$key]] = $users[$value];
					}

				break;

				case 'id_campagne':
					ATF::campagne()->q->reset()->where("campagne", $value);
					if($campagne = ATF::campagne()->select_row()){
						$data[$entete[$key]] = $campagne["id_campagne"];
					} else {
						$data[$entete[$key]] = ATF::campagne()->insert(array("campagne"=> $value));
					}

				break;

				case 'id_secteur_geographique':
					ATF::secteur_geographique()->q->reset()->where("secteur_geographique", $value);
					if($secteur_geographique = ATF::secteur_geographique()->select_row()){
						$data[$entete[$key]] = $secteur_geographique["id_secteur_geographique"];
					} else {
						$data[$entete[$key]] = ATF::secteur_geographique()->insert(array("secteur_geographique"=> $value));
					}

				break;


				case 'id_famille':
					ATF::famille()->q->reset()->where("famille", $value);
					if($fam = ATF::famille()->select_row()){
						$data[$entete[$key]] = $fam["id_famille"];
					}else{
						throw new errorATF($entete[$key]." ".$value." non trouvé");
					}
				break;

				case 'id_contact_facturation':
				case 'id_contact_commercial':
				case 'id_contact_signataire':
				case 'id_prospection':
					if($value !== " "){

						ATF::contact()->q->reset()
							->where("nom", "%".ATF::db()->real_escape_string($value)."%","OR","findContact", "LIKE")
							->where("prenom", "%".ATF::db()->real_escape_string($value)."%","OR","findContact", "LIKE")
							->where("CONCAT(prenom, ' ', nom)", "%".ATF::db()->real_escape_string($value)."%","OR","findContact", "LIKE")
							->where("CONCAT(nom, ' ', prenom)", "%".ATF::db()->real_escape_string($value)."%","OR","findContact", "LIKE");
							//->where("id_societe", $data["id_societe"]);


						;
						if( $contact = ATF::contact()->select_row() ){
							$contacts[$value] = $contact["id_contact"];
						}else{
							ATF::contact()->q->setToString();
							log::logger(ATF::contact()->select_row() , $log_import);


							throw new errorATF($entete[$key]." ".$value." societe ".ATF::societe()->select($data["id_societe"], "societe")." non trouvé");
						}
					}

				break;

				case 'facturation_id_pays':
				case 'id_pays' :
					ATF::pays()->q->reset()->where("pays", $value);

					if($pays = ATF::pays()->select_row()){
						$data[$entete[$key]] = $pays["id_pays"];
					}
				break;

				case "id_devise":
					if(!$devises[$value]){
						ATF::devise()->q->reset()->where("devise", $value);
						if( $devise = ATF::devise()->select_row() ){
							$devises[$value] = $devise["id_devise"];
						}else{
							throw new errorATF($entete[$key]." ".$value." non trouvée");
						}
					}

					$data[$entete[$key]] = $devises[$value];

				break;

				default:
					$data[$entete[$key]] = $value;
				break;
			}
		}
	}


	try{
		update($data);
	} catch (errorATF $e) {
		//L'id donné ne doit pas etre correct, on check pour recuperer le bon ID par rapport à societe et adresse et on retente l'insert
		ATF::societe()->q->reset()->where("societe", ATF::db()->real_escape_string($data["societe"]))
								  ->where("adresse", ATF::db()->real_escape_string($data["adresse"]));
		if($soc = ATF::societe()->select_row()){
			if($soc["id_societe"] !== $data["id_societe"]){
				$data["id_societe"] = $soc["id_societe"];
				try{
					update($data);
				}catch(errorATF $e){
					$err++;
					log::logger($data["societe"]. " / ".$data["id_societe"]." --> ".$data["adresse"], $log_erreur);
				}

			}else{
				$err++;
				log::logger($data["societe"]. " / ".$data["id_societe"]." --> ".$data["adresse"], $log_erreur);
			}
		}
	}

}

echo "Erreur --> ".$err;


// Rollback la transaction
//ATF::db()->rollback_transaction();
// Valide la trnasaction
ATF::db()->commit_transaction();
echo "========= FIN DE SCRIPT =========\n";



function update($data){
	if(date("Y-m-d", $data["date"]) == "1970-01-01") unset($data["date"]);

	log::logger(ATF::societe()->select($data["id_societe"], "societe")." ".ATF::societe()->select($data["id_societe"], "adresse")." --> ".$data["societe"]." ".$data["adresse"], $log_import_update);

	//log::logger($data , $log_import);
	ATF::societe()->u($data);

}