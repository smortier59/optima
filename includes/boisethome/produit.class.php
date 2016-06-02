<?
class produit_boisethome extends classes_optima {
	// Mapping prévu pour un autocomplete sur article
	public static $autocompleteMapping = array(
		array("name"=>'produit', "mapping"=>0),
		array("name"=>'unite', "mapping"=>1),
		array("name"=>'description', "mapping"=>2),
		array("name"=>'lambda', "mapping"=>3),
		array("name"=>'id_produit', "mapping"=>4),
		array("name"=>'prix', "mapping"=>5),
		array("name"=>'prix_achat', "mapping"=>6)
	);

	function __construct() {
		parent::__construct();
		$this->table = "produit";

		$this->colonnes['fields_column'] = array(
			'produit.produit'
			,'produit.description'
			,'produit.unite'
			,'prix'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'prix_achat'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'produit.lambda'
		);

		$this->colonnes['primary'] = array(
			'produit'
			,'description'
			,'unite'
			,'lambda'=>array("targetCols"=>1)
		);
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>2);

		$this->colonnes['panel']['articles'] = array(
			"articles"=>array("custom"=>true)
		);
		$this->panels['articles'] = array("visible"=>true, 'nbCols'=>1);

		$this->colonnes['panel']['total'] = array(
			"prix_achat"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);

		$this->onglets = array(
			'produit_article'=>array('opened'=>true)
		);

		$this->autocomplete = array(
			"field"=>array("produit","description")
			,"show"=>array("produit","description")
			,"popup"=>array("produit","description")
		);

		$this->colonnes['bloquees']['select'] = array("articles","prix_achat");

		$this->fieldstructure();
	}

	/**
     * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$infos_ligne = json_decode($infos["values_".$this->table]["articles"],true);
		$this->infoCollapse($infos);

		$this->check_field($infos);

		if(!$infos_ligne){
			throw new error(ATF::$usr->trans("produit_article_inexistant"));
		}

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		//produit
		unset($infos["prix_achat"]);
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		//article
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("produit_article.","",$k_unescape)]=$i;
				unset($item[$k]);
			}
			$itemClean = array(
				"article"=>$item["article"],
				"id_article"=>$item["id_article_fk"],
				"id_produit"=>$last_id,
				"quantite"=>$item["quantite"]?$item["quantite"]:0
			);
			ATF::produit_article()->insert($itemClean,$s);
		}

		//*****************************Transaction FIN****************************
		ATF::db($this->db)->commit_transaction();

		if(is_array($cadre_refreshed)){
			ATF::produit()->redirection("select",$last_id);
		}
		return $this->cryptId($last_id);
	}

	/**
     * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$id_produit=$this->decryptId($infos["produit"]["id_produit"]);

		$infos_ligne = json_decode($infos["values_".$this->table]["articles"],true);
		$this->infoCollapse($infos);

		$this->check_field($infos);

		if(!$infos_ligne){
			throw new error(ATF::$usr->trans("produit_article_inexistant"));
		}

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		//produit
		unset($infos["prix_achat"]);
		parent::update($infos,$s,NULL,$var=NULL,NULL,true);

		//on supprime tous les articles
		ATF::produit_article()->q->reset()->where("produit_article.id_produit",$id_produit);
		$produit_article=ATF::produit_article()->sa();
		foreach($produit_article as $key=>$item){
			ATF::produit_article()->delete(array("id"=>$item["id_produit_article"]));
		}

		//on insère les nouveaux article
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("produit_article.","",$k_unescape)]=$i;
				unset($item[$k]);
			}
			$itemClean = array(
				"article"=>$item["article"],
				"id_article"=>$item["id_article_fk"],
				"id_produit"=>$id_produit,
				"quantite"=>$item["quantite"]?$item["quantite"]:0
			);
			ATF::produit_article()->insert($itemClean,$s);
		}

		//*****************************Transaction FIN****************************
		ATF::db($this->db)->commit_transaction();

		if(is_array($cadre_refreshed)){
			ATF::produit()->redirection("select",$id_produit);
		}
		return $this->cryptId($id_produit);
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		$this->q
			->from("produit","id_produit","produit_article","id_produit")
			->from("produit_article","id_article","article","id_article")
			->from("article","id_marge","marge","id_marge")
			->addField("ROUND(SUM(produit_article.quantite*article.prix_achat*marge.taux),2)","prix")
			->addField("ROUND(SUM(produit_article.quantite*article.prix_achat),2)","prix_achat")
			->addGroup("produit.id_produit");
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître les produits déjà insérés par l'utilisateur
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
				->setStrict()
				->from("produit","id_produit","produit_article","id_produit")
				->from("produit_article","id_article","article","id_article")
				->from("article","id_marge","marge","id_marge")
				->where("produit.produit","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				->where("produit.description","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				->addField("produit.produit","produit")
				->addField("produit.unite","unite")
				->addField("produit.description","description")
				->addField("produit.lambda","lambda")
				->addField('produit.id_produit',"id_produit")
				->addField("ROUND(SUM(produit_article.quantite*article.prix_achat*marge.taux),2)","prix")
				->addField("ROUND(SUM(produit_article.quantite*article.prix_achat),2)","prix_achat")
				->setStrict(1)
				->setToString()
				->addGroup("produit.id_produit");
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