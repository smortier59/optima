<?
/**
* Classe facturation_attente
* @package Optima
* @subpackage Cléodis
*/
class facturation_attente extends classes_optima {
	function __construct() {
		$this->table="facturation_attente";
		parent::__construct();
		$this->fieldstructure();
	}


	/**
	* Surcharge de l'insert
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		return parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
	}


	public function envoye_facturation($periodeDebut=null, $periodeFin=null){
		$this->q->reset()->where("envoye", "non");
		foreach ($this->select_all() as $key => $value) {
			$email = get_object_vars(json_decode($value["mail"]));
			$email["texte"] = str_replace("u00e9", "é", $email["texte"]);

			$id_facture = $value["id_facture"];
			$facture = ATF::facture()->select($id_facture);
			$table = $value["nom_table"];
			$path = get_object_vars(json_decode($value["path"]));

			$id_facturation = $value["id_facturation"];
			$canSend = true;
			if ($periodeDebut && date('Ymd' , strtotime($facture["date_periode_debut"])) < date('Ymd' , strtotime($periodeDebut))) $canSend = false;
			if ($periodeFin && date('Ymd' , strtotime($facture["date_periode_fin"])) > date('Ymd' , strtotime($periodeFin))) $canSend = false;

			if ($canSend) {
				if ($facture["envoye"] !== "oui") {
					try {
						$facture_info = ATF::facture()->select($id_facture);
						$suivi_message = "Envoi de la facture ".$facture_info['ref'].
										" au client ".ATF::societe()->select($facture_info["id_societe"], "societe")." (email: ".$email["email"].") ".
										" pour l'affaire ".ATF::affaire()->select($facture_info["id_affaire"], "ref");

						if ($facture_info["envoye"] != 'oui') {

							ATF::affaire()->mailContact($email,$id_facture,"facture",$path);
							ATF::facture()->u(array("id_facture"=> $id_facture, "envoye"=> "oui", "date_envoi" => date("Y-m-d")));
						} else {
							$this->u(array("id_facturation_attente"=> $value["id_facturation_attente"], "erreur"=> "Facture déja envoyée"));
						}

						$this->u(array("id_facturation_attente"=> $value["id_facturation_attente"], "envoye"=> "oui"));
						ATF::facturation()->u(array("id_facturation"=> $id_facturation, "envoye"=> "oui"));
					} catch (errorATF $e) {
						$this->u(array("id_facturation_attente"=> $value["id_facturation_attente"], "envoye"=> "erreur", "erreur"=>$e->getMessage()));
						$suivi_message = "Erreur lors de l'envoi de la facture  ".$facture_info['ref']." au client ".ATF::societe()->select($facture_info["id_societe"], "societe")."\nRaison: ".$e->getMessage();
					}

					if ($facture_info) {
						$suivi = array(
							"id_societe"=> $facture_info["id_societe"]
							,"id_affaire"=> $facture_info["id_affaire"]
							,"type_suivi"=>'Comptabilité'
							,"texte"=>$suivi_message
							,'public'=>'oui'
							,'id_contact'=>NULL
							,'suivi_societe'=>NULL
							,'suivi_notifie'=>NULL
							,'champsComplementaire'=>NULL
						);
						$suivi["no_redirect"] = true;

						ATF::suivi()->insert($suivi);
					}
				} else {
					$this->u(array("id_facturation_attente"=> $value["id_facturation_attente"], "erreur"=> "Facture déja envoyée"));
				}
			}
		}
	}
};