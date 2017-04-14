<?
/** 
* Classe alerte_imprimante
* @author Cyril Charlier <ccharlier@absystech.fr>
* @package Optima
* @subpackage Absystech
*/
class alerte_imprimante_absystech extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
        $this->table='alerte_imprimante';
		$this->colonnes['fields_column']  = array(
			'alerte_imprimante.id_stock'
			,'alerte_imprimante.code'
			,'alerte_imprimante.ville'
			,'alerte_imprimante.message'
			,'alerte_imprimante.date'
			,'alerte_imprimante.notification'
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
		log::logger($get,'ccharlier');
		// Gestionde la page
		if (!$get['page']) $get['page'] = 0;

		$this->q->reset();
		if ($get['notification'] == "oui") {
			$this->q->where("alerte_imprimante.notification","oui");
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
		header("ts-total-row: ".$data['count']);
		header("ts-max-page: ".ceil($data['count']/$get['limit']));
		header("ts-active-page: ".$get['page']);
		$return = $data['data'];
		}
		return $return;
	}
	/**
	* Permet d'ajouter une ou plusieurs notifications
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/  	
	public function _POST($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		$return = array();
        try {
        	foreach ($post['alerts'] as $k=>$i) {
        		$toinsert[]= array(
        			"code"=>$i['code']
        			,"message"=>$i['message']
        			,"id_stock"=>$post['id_stock']

        		)
        	}
        	$result = $this->multi_insert($post);    		

		} catch (errorATF $e) {
  			throw new errorATF($e->getMessage(),500);
		}
        $return['result'] = true;
        $return['id_user'] = $result;
        return $return;
	}


}

?>
