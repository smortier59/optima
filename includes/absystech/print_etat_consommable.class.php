<?
/** 
* Classe print_etat_consommable
* @author Cyril Charlier <ccharlier@absystech.fr>
* @package Optima
* @subpackage Absystech
*/
class print_etat_consommable_absystech extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->field_nom = 'date';
        $this->table='print_etat_consommable';
		$this->colonnes['fields_column']  = array(
			'id_print_etat_consommable'
			,'date'
			,'id_stock'
			,'id_print_consommable'
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
			"print_consommable.designation",
			"date",
			"couleur",
			"duree"
		);

		$this->q->reset();
		if ($get['id_stock']) {
			$this->q->where("id_stock",$get['id_stock']);
		}
		$this->q->addField($colsData);
		$this->q->from("print_etat_consommable", "id_print_consommable" , "print_consommable" , "id_print_consommable")
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
        $return['id_print_etat_consommable'] = $result;
        return $return;
	}
}

?>
