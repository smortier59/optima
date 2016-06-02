<?
class marge_boisethome extends classes_optima {
	// Mapping prévu pour un autocomplete sur article
	public static $autocompleteMapping = array(
		array('name'=> 'taux', 'mapping'=> 0)
		,array('name'=>'id', 'mapping'=> 1)
		,array('name'=> 'marge', 'mapping'=> 2)
	);

	function __construct() {
		parent::__construct();
		$this->table = "marge";
		$this->controlled_by = "article";

		$this->colonnes['fields_column'] = array(
			'marge.marge'
			,'marge.taux'
		);

		$this->colonnes['primary']=array('marge','taux');

		$this->autocomplete = array(
			"field"=>array("taux","marge")
			,"show"=>array("taux","marge")
			,"popup"=>array("taux","marge")
		);
		$this->fieldstructure();
	}

	/**
    * Surcharge de la méthode autocomplete pour récupérer 
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {
		if ($infos["limit"]>25) return; // Protection nombre d'enregistrements par page
		//if (strlen($infos["query"])>0) {
			$data = array();
			$searchKeywords = stripslashes(urldecode($infos["query"]));
			// Récupérer les produits
			$this->q->reset()
				->where("marge.marge","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				->addField("marge.taux","taux")
				->addField("marge.id_marge","id")
				->addField("marge.marge","marge")
				->setStrict(1)
				->setToString();
			$queries[] = $this->sa();
			$q = new querier();
			$q->setLimit($infos["limit"])->setPage($infos["start"]/$infos["limit"]);
			if ($result = ATF::db($this->db)->union($queries,$q)) {
				// On met en valeur la chaîne recherchée dans les réponses
				$replacement = ATF::$html->fetch("search_replacement.tpl.htm","sr");
				foreach ($result["data"] as $k => $i) {
					foreach ($result["data"][$k] as $k_ => $i_) { // Mettre en valeur
						$result_final["data"][$k][$k_] = strlen($infos["query"])>0 ? preg_replace("/".$infos["query"]."/i", $replacement, $i_) : $i_;
					}
					$result_final["data"][$k][$k_+1] = $result["data"][$k][1];
				}
			}
			ATF::$json->add("totalCount",$result_final["count"]);
		//}
		ATF::$cr->rm("top");
		return $result_final["data"];
	}
}