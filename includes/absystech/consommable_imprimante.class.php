<?
/** 
* Classe etat_imprimante
* @author Cyril Charlier <ccharlier@absystech.fr>
* @package Optima
* @subpackage Absystech
*/
class consommable_imprimante_absystech extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->field_nom = 'designation';
        $this->table='consommable_imprimante';
		$this->colonnes['fields_column']  = array(
			'id_consommable_imprimante'
			,'designation'
			,'code'
			,'duree'
			,'prix'
			,'ref_imprimante'
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
		if (!$get['tri']) $get['tri'] = "code";
		if (!$get['trid']) $get['trid'] = "asc";
		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;
		// Gestionde la page
		if (!$get['page']) $get['page'] = 0;
		

		$this->q->reset();
		if ($get['id_consommable_imprimante']) {
			$this->q->where("id_consommable_imprimante",$get['id_consommable_imprimante']);
		}
		if ($get['ref_imprimante']) {
			$this->q->where("ref_imprimante",$get['ref_imprimante']);
		}
		$this->q->setLimit($get['limit']); 
		$this->q->setCount();
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
		if($get['id_consommable_imprimante']){
			$return= $data['data'][0];
		}else{
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);
			$return = $data['data'];
		}
		return $return;
	}
	/**
	* Permet d'ajouter un consommable sur la reference de l'imprimante
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
        $return['id_consommable'] = $result;
        return $return;
	}
	/**
	* Permet de supprimer
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @package Telescope Hyperviseur CC
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient id_consommable_imprimante 
	* @return boolean
	*/  	
	public function _DELETE($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		$return = array();
        try {	  
			$result = $this->delete($post);
		} catch (errorATF $e) {
  			throw new errorATF($e->getMessage(),500);
		}

        $return['result'] = true;
        return $return;
	}


}

?>
