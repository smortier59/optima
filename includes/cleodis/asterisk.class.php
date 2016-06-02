<?
/** 
* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
* @package Optima
*/
require_once dirname(__FILE__)."/../asterisk.class.php";
class asterisk_cleodis extends asterisk {	
	/**
	* Retourne le numéro de téléphone du destinataire en fonction du callerId
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $callerId numéro de téléphone
	* @return int Numéro de téléphone de l'appelant, NULL si pas de correspondance trouvée
	*/
	public function getAgentConcerned($callerId){
		if ($callerId) {
			if ($contact = $this->getContactFromCallerId($callerId)) {
				// Soit c'est un numéro de contact, dont on récupère la société
				$id_societe = $contact["id_societe"];
			} elseif ($id_societe = $this->getSocieteFromCallerId($callerId,true)) {
				// Soit c'est un numéro d'une société
	
			}
			if ($id_societe) {
				$code_client = ATF::societe()->select($id_societe,'code_client');
			}
			switch(substr($code_client,0,1)) {
				case "C": 
				case "M":
					// Séverine MAZARS 
					return 201;
			}
		}
	}
};

class asterisk_cleodisbe extends asterisk_cleodis { };
class asterisk_cap extends asterisk_cleodis { };
class asterisk_exactitude extends asterisk_cleodis { };
?>