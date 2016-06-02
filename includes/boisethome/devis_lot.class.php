<?
class devis_lot_boisethome extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "devis_lot";
		$this->controlled_by = "devis";
		$this->colonnes['fields_column'] = array(
			 'devis_lot.libelle'
			,'devis_lot.id_devis'
			,'devis_lot.optionnel'
			,"devis_lot.payer_pourcentage"
			,'vendu_matiere'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,'cout_matiere'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,'vendu_mo'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,'cout_mo'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>100)
			,'actions'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"actionsDevis_lot","width"=>80)
		);

		$this->colonnes['primary'] = array(
			"libelle"
			,"id_devis"=>array("autocomplete"=>array(
				"mapping"=>ATF::devis()->autocompleteMapping
			))
			,"optionnel"
			,"payer_pourcentage"
		);

		$this->colonnes['bloquees']['insert'] = array('id_devis');
		$this->fieldstructure();
		$this->field_nom = "libelle";
		$this->onglets = array('devis_lot_produit');

		$this->addPrivilege("selectFromDevis");

		$this->colonnes['ligne'] =  array(
			"devis_lot.libelle"
			,"devis_lot.id_devis"
			,"devis_lot.optionnel"
			,"devis_lot.payer_pourcentage"
		);
	}

	/**
	* Retourne les devis_lot_produit d'un devis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param Objet querier
	*/
	 public function selectFromDevis($post,$s) {
	 	if (!$post["id_devis"]) return;

		$q = ATF::_s("pager")->create($post["pager"],NULL,true);
		$q->reset('where')
			->where("devis_lot.id_devis",$this->decryptId($post["id_devis"]));

		$post["function"]="selectFromDevisAfter";
		$res = $this->extJSgsa($post,$s);

		foreach ($res as $key => $value) {
			$res[$key]["devis_lot.id_devis_lot"] = $this->decryptId($res[$key]["devis_lot.id_devis_lot"]);
		}

		return $res;
	}

  	/**
	* Retourne les produits d'un devis pour le grid des devis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
  	public function selectFromDevisAfter() {
		$this->q->reset('field,limit,page,order')->addField(util::keysOrValues($this->colonnes['ligne']))->addOrder("devis_lot.id_devis_lot","asc");
		return $this->sa();
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
			->from("devis_lot","id_devis_lot","devis_lot_produit","id_devis_lot")
			->from("devis_lot","id_devis_lot","facture","id_devis_lot")
			->from("devis_lot","id_devis","devis","id_devis")
			->from("devis_lot_produit","id_devis_lot_produit","devis_lot_produit_article","id_devis_lot_produit")
			->from("devis_lot_produit_article","id_article","article","id_article")
			->from("devis_lot_produit_article","id_marge","marge","id_marge")
			->where("devis.etat","gagne")
			->addField("ROUND(devis_lot_produit.quantite * SUM(IF(article.nature='matiere',devis_lot_produit_article.quantite*devis_lot_produit_article.prix_achat,0)),2)","cout_matiere")
			->addField("ROUND(devis_lot_produit.quantite * SUM(IF(article.nature='matiere',devis_lot_produit_article.quantite*devis_lot_produit_article.prix_achat*marge.taux,0)),2)","vendu_matiere")
			->addField("ROUND(devis_lot_produit.quantite * SUM(IF(article.nature='mo',devis_lot_produit_article.quantite*devis_lot_produit_article.prix_achat,0)),2)","cout_mo")
			->addField("ROUND(devis_lot_produit.quantite * SUM(IF(article.nature='mo',devis_lot_produit_article.quantite*devis_lot_produit_article.prix_achat*marge.taux,0)),2)","vendu_mo")
			->addField("facture.id_facture","id_facture")
			->addGroup("devis_lot.id_devis_lot")
		;
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as$k=>$i) {
			$return['data'][$k]['allowFacture'] = !$i['id_facture'] && ATF::$usr->privilege('facture','insert');
		}
		return $return;
	}
}