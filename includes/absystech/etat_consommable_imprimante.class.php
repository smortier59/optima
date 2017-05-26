<?
/** 
* Classe etat_imprimante
* @author Cyril Charlier <ccharlier@absystech.fr>
* @package Optima
* @subpackage Absystech
*/
class etat_consommable_imprimante_absystech extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->field_nom = 'date';
        $this->table='etat_consommable_imprimante';
		$this->colonnes['fields_column']  = array(
			'id_etat_consommable_imprimante'
			,'date'
			,'id_stock'
			,'id_consommable_imprimante'
		); 
	}
	
	/**
	* Fonction _GET pour telescope
	* @package Telescope - Hyperviseur CC
	* @author Charlier Cyril <ccharlier@absystech.fr>
	* @param $get array.
	* @param $post array Argument obligatoire.
	* @return boolean | integer
	*/
	public function _GET($get,$post) {
		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "date";
		if (!$get['trid']) $get['trid'] = "desc";
		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;
		// Gestionde la page
		if (!$get['page']) $get['page'] = 0;
		
		$colsData = array(
			"consommable_imprimante.designation",
			"date",
			"couleur_consommable",
			"duree"
		);

		$this->q->reset();
		if ($get['id_stock']) {
			$this->q->where("id_stock",$get['id_stock']);
		}
		$this->q->addField($colsData);
		$this->q->from("etat_consommable_imprimante", "id_consommable_imprimante" , "consommable_imprimante" , "id_consommable_imprimante")
				->setLimit($get['limit']) 
				->setCount();
		$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}				
			}
		}
		header("ts-total-row: ".$data['count']);
		header("ts-max-page: ".ceil($data['count']/$get['limit']));
		header("ts-active-page: ".$get['page']);
		$return = $data['data'];

		return $return;
	}
	/**
	* Permet d'ajouter un consommable livré pour une imprimante
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @package Telescope\Printer
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/  	
	public function _POST($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		$return = array();
		// ajout de toutes les cartouches & de leur état

        try {	  
			$result = $this->insert($post);
		} catch (errorATF $e) {
  			throw new errorATF($e->getMessage(),500);
		}
 
        $return['result'] = true;
        $return['id_etat_consommable_imprimante'] = $result;
        return $return;
	}
}

?>
