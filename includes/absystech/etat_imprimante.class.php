<?
/** 
* Classe etat_imprimante
* @author Cyril Charlier <ccharlier@absystech.fr>
* @package Optima
* @subpackage Absystech
*/
class etat_imprimante_absystech extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
        $this->table='etat_imprimante';
		$this->colonnes['fields_column']  = array(
			'etat_imprimante.id_stock'
			,'etat_imprimante.name'
			,'etat_imprimante.date'
			,'etat_imprimante.color'
			,'etat_imprimante.current'
			,'etat_imprimante.max'
			,'etat_imprimante.type'
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
		if (!$get['tri']) $get['tri'] = "id_stock";
		if (!$get['trid']) $get['trid'] = "desc";
		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;
		// Gestionde la page
		if (!$get['page']) $get['page'] = 0;

		$this->q->reset();
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
		header("ts-total-row: ".$data['count']);
		header("ts-max-page: ".ceil($data['count']/$get['limit']));
		header("ts-active-page: ".$get['page']);
		$return = $data['data'];
		return $return;
	}
	/**
	* Permet d'ajouter une ou plusieurs informations sur l'imprimante
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
		$toner = json_decode($post['toner'],true);
		foreach ($toner as $k=>$i) {
			$toinsert[]= array(
				"name"=>$i['name']
				,"current"=>$i['current']
				,"color"=>$i['id_stock']
				,"max"=>$i['max']
			);
		}
		// Ajouter maintenant les cout copies
		// 
        try {	  
        	ATF::db($this->db)->begin_transaction();  		
			$result = $this->multi_insert($toinsert);
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			log::logger($e->getMessage(),'ccharlier');
			throw new errorATF('Erreur Insert',500);
		}
		ATF::db($this->db)->commit_transaction();
		log::logger('result','ccharlier');

		log::logger($result,'ccharlier');
        $return['result'] = true;
        $return['id_etat_imprimante'] = $result;
        log::logger($return,'ccharlier');
        return $return;
	}


}

?>
