<?
class article_boisethome extends classes_optima {
	// Mapping prévu pour un autocomplete sur article
	public static $autocompleteMapping = array(
		array('name'=> 'article', 'mapping'=> 0)
		,array('name'=>'id', 'mapping'=> 1)
		,array('name'=> 'nature', 'mapping'=> 2, 'type'=>'string')
		,array('name'=> 'unite', 'mapping'=> 3, 'type'=>'string' )
		,array('name'=> 'conditionnement', 'mapping'=> 4)
		,array('name'=> 'prix_achat', 'mapping'=> 5)
		,array('name'=> 'id_fournisseur', 'mapping'=> 6)
		,array('name'=> 'fournisseur', 'mapping'=> 7)
		,array('name'=> 'id_marge', 'mapping'=> 8)
		,array('name'=> 'marge', 'mapping'=> 9)
		,array('name'=> 'visible', 'mapping'=> 10)
	);

	function __construct() {
		parent::__construct();
		$this->table = "article";

		$this->colonnes['fields_column'] = array(
			'article.article'
			,'article.nature'
			,'prix_comptable'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,'stock'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","width"=>100)
			,'stock_alert'=>array("custom"=>true,"align"=>"right","width"=>100)
			,'stock_valeur'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,"article.id_fournisseur"=>array("autocomplete"=>array(
				"function"=>"autocompleteFournisseurs"
			))
			,'conditionnement'=>array("rowEditor"=>"setInfos")
			,'qte_commandee'=>array("rowEditor"=>"setInfos")
		);

		$this->colonnes['primary']=array(
			'article'
			,'conditionnement'=>array("xtype"=>"textarea")
			,'ref'=>array("targetCols"=>1)
			,'nature'=>array("targetCols"=>1)
			,'unite'=>array("targetCols"=>1)
			,'destination'=>array("targetCols"=>1)
			,'visible'=>array("targetCols"=>1)
			,'qte_cond'=>array("targetCols"=>2)
			,'commentaire'=>array("xtype"=>"textarea","targetCols"=>2)
		);
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>2);

		$this->colonnes['panel']['valeur'] = array("id_marge","prix_achat","lambda");
		$this->panels['valeur'] = array("visible"=>true,'nbCols'=>3);

		$this->colonnes['panel']['approvisionnement'] = array("id_fournisseur","delai","stock_mini","qte_commandee","date_commande");
		$this->panels['approvisionnement'] = array("visible"=>true,'nbCols'=>3);

		$this->autocomplete = array(
			"field"=>array("article","nature")
			,"show"=>array("article","nature")
			,"popup"=>array("article","nature")
		);
		$this->onglets = array('produit_article','stock','article_history');
		$this->field_nom = "article";
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->fieldstructure();

		$this->addPrivilege("setInfos","update");
		$this->addPrivilege("barcode");
	}

	/**
    * Surcharge de la méthode update pour conserver l'historique des prix et fournisseurs lorsqu'ils changent
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
    */
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);
		if ($old = $this->select($infos["id_article"])) {
			if($infos["prix_achat"]!=$old["prix_achat"] || $infos["id_fournisseur"]!=$old["id_fournisseur"]){
				ATF::article_history()->insert(array(
					"id_article" => $this->decryptId($infos["id_article"]),
					"prix_achat" => $old["prix_achat"],
					"id_fournisseur" => $old["id_fournisseur"],
					"id_user" => ATF::$usr->getID()
				));
			}
		}
		return parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître les produits déjà insérés par l'utilisateur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*  $infos[limit]
	*  $infos[query]
	*  $infos[start]
	*  $infos[escapefieldstable]
	*  $infos[id_produit]
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {
		if ($infos["limit"]>25) return; // Protection nombre d'enregistrements par page
		if (!$infos["limit"]) $infos["limit"]=25;
		if (!$infos["start"]) $infos["start"]=0;
		$data = array();
		$searchKeywords = stripslashes(urldecode($infos["query"]));
		// Récupérer les produits
		$this->q->reset()
			->setStrict()
			->addField("article.article","article")
			->addField('article.id_article',"id")
			->addField("article.nature","nature")
			->addField("article.unite","unite")
			->addField("article.conditionnement","conditionnement")
			->addField("article.prix_achat","prix_achat")
			->addField("article.id_fournisseur","id_fournisseur")
			->addField("societe.societe","fournisseur")
			->addField("article.id_marge","id_marge")
			->addField("marge.taux","marge")
			->addField("article.visible","visible")
			->from("article","id_fournisseur","societe","id_societe")
			->from("article","id_marge","marge","id_marge")
			->setStrict(1);
		if ($infos["id_produit"]) {
			// Recherche de tous les articles d'un produit
			$this->q->where("produit_article.id_produit",$infos["id_produit"])
				->addField("produit_article.article","article")
				->addField("produit_article.quantite","quantite")
				->from("article","id_article","produit_article","id_article");

			if ($result = $this->sa()) {
				foreach ($result as $r) {
					$row = array();
					foreach ($r as $field => $value) {
						if ($field=="id_fournisseur") $field = "id_fournisseur_fk";
						if ($field=="fournisseur") $field = "id_fournisseur";
						if ($field=="id_marge") $field = "id_marge_fk";
						if ($field=="marge") $field = "id_marge";
						if ($field=="id") $field = "id_article_fk";
						if ($field=="article") $field = "id_article";
						$row[util::extJSEscapeDot($infos["escapefieldstable"].".".$field)] = $value;
					}
					$return[] = $row;
				}
			}
			return $return;

		} else {
			$this->q
				->setToString()
				->where("article.article","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE');

			$queries[] = $this->sa();
			$q = new querier();
			$q->setLimit($infos["limit"])->setPage($infos["start"]/$infos["limit"]);
			if ($result = ATF::db($this->db)->union($queries,$q)) {
				// On met en valeur la chaîne recherchée dans les réponses
				if (strlen($infos["query"])>0) {
					$replacement = ATF::$html->fetch("search_replacement.tpl.htm","sr");
				}
				foreach ($result["data"] as $k => $i) {
					foreach ($result["data"][$k] as $k_ => $i_) { // Mettre en valeur
						$result_final["data"][$k][$k_] = $replacement ? preg_replace("/".$infos["query"]."/i", $replacement, $i_) : $i_;
					}
					$result_final["data"][$k][$k_+1] = $result["data"][$k][1];
				}
			}
			ATF::$json->add("totalCount",$result_final["count"]);
			ATF::$cr->rm("top");
			return $result_final["data"];
		}
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @return string
    */
	public function default_value($field){
		$this->q->reset()->addOrder("article.id_article","desc")->setLimit(1)->setDimension('row');
		$last = $this->select_all();
		switch ($field) {
			default:
				return $last[$field];
				break;
		}
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
			->from("article","id_article","stock","id_article")
			->from("article","id_fournisseur","societe","id_societe","societe__id_fournisseur")
			->addField("stock_mini")
			->addField("article.prix_achat*article.qte_cond","prix_comptable")
			->addField("SUM(stock.mouvement)","stock")
			->addField("IF(SUM(stock.mouvement)<stock_mini, 'oui', 'non')","stock_alert")
			->addField("article.prix_achat*SUM(stock.mouvement)","stock_valeur")
			->addGroup("article.id_article")
		;
		$return = parent::select_all($order_by,$asc,$page,$count);
		/*
		foreach ($return['data'] as$k=>$i) {
			$return['data'][$k]['allowFacture'] = !$i['id_facture'] && ATF::$usr->privilege('facture','insert');
		}
		*/
		//log::logger($return, 'alahlah');
		return $return;
	}

    /** Export en PDF des code barres des articles choisis
     * @author Yann GAUTHERON <ygautheron@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function barcode(&$infos,&$s){
	 	$infos["display"] = true;
		$this->q->reset();
		$this->setQuerier($s["pager"]->create($infos['onglet'])); // Recuperer le querier actuel
		$this->q->addField("article.unite");
		$this->q->setLimit(-1);
		$infos = $this->sa();
		return ATF::pdf()->barcode($infos['data'],$s);
    }


	public function setInfos($infos){
		if ($infos["conditionnement"]) {
			return $this->u(array(
				"id_article"=> $this->decryptId($infos["id_article"]),
				"conditionnement" => $infos["conditionnement"]
			));
		}
		if ($infos["qte_commandee"]) {
			return $this->u(array(
				"id_article"=> $this->decryptId($infos["id_article"]),
				"qte_commandee" => $infos["qte_commandee"]
			));
		}
	}
}