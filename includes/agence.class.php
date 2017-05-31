<?
/** Classe agence
* @package Optima
*/
class agence extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'agence.agence'
			,'agence.ville'
		);
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse"
			,"adresse_2"
			,"adresse_3"
			,"cp"
			,"ville"
			,"id_pays"
			,"tel"
			,"fax"
		);
		$this->fieldstructure();
		$this->panels['coordonnees'] = array("visible"=>true);
	}
		/**
	* Permet de récupérer la liste des agences pour telescope
	* @package Telescope
	* @author Cyril Charlier <ccharlier@absystech.fr>
	* @param $get array Paramètre de filtrage, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	//$order_by=false,$asc='desc',$page=false,$count=false,$noapplyfilter=false
	public function _GET($get,$post) {
		// Gestion du tri
		if (!$get['trid']) $get['trid'] = "asc";
		if (!$get['tri']) $get['tri'] = "agence";

		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			"agence.id_agence"=>array(),
			"agence.agence"=>array(),
			"agence.adresse"=>array(),
			"agence.cp"=>array(),
			"agence.ville"=>array(),
			"agence.id_pays"=>array(),
			"agence.tel"=>array(),
			"agence.fax"=>array(),
		);

		$this->q->reset();

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get["search"]);
		}

		if ($get['id']) {
			$this->q->where("id_agence",$get['id'])->setLimit(1);
		} else {
			$this->q->setLimit($get['limit']);
		}



		switch ($get['tri']) {
			case 'agence':
				$get['tri'] = "agence.".$get['tri'];
			break;
		}

		if($get["filter"]){
			foreach ($get["filter"] as $key => $value) {
				if (strpos($key, 'agence') !== false) {
					$this->q->addCondition(str_replace("'", "",$key), str_replace("'", "",$value), "AND");
				}
			}
		}

		$this->q->addField($colsData);
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

		if ($get['id']) {
	        $return = $data['data'][0];
		} else {
			// Envoi des headers
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);

      	$return = $data['data'];
		}

		return $return;
	}
};
?>