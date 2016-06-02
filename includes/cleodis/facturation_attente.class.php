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


	public function envoye_facturation(){
		$this->q->reset()->where("envoye", "non");
		foreach ($this->select_all() as $key => $value) {
			$email = get_object_vars(json_decode($value["mail"]));
			$email["texte"] = str_replace("u00e9", "é", $email["texte"]);

			$id_facture = $value["id_facture"];
			$table = $value["nom_table"];
			$path = get_object_vars(json_decode($value["path"]));
			$id_facturation = $value["id_facturation"];
	
			ATF::affaire()->mailContact($email,$id_facture,"facture",$path);

			$this->u(array("id_facturation_attente"=> $value["id_facturation_attente"], "envoye"=> "oui"));
			ATF::facturation()->u(array("id_facturation"=> $id_facturation, "envoye"=> "oui"));
		}
	}
};

class facturation_attente_cleodisbe extends facturation_attente { };
class facturation_attente_cap extends facturation_attente { };
class facturation_attente_exactitude extends facturation_attente { };
?>