<?
class devis_lot_produit_article_boisethome extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "devis_lot_produit_article";
		$this->controlled_by = "devis";
		$this->colonnes['fields_column'] = array(
			'devis_lot_produit_article.id_devis_lot_produit_article'
			,'devis_lot_produit_article.id_article'
			,'devis_lot_produit_article.quantite'
			,'devis_lot_produit_article.article'
			,'devis_lot_produit_article.unite'
			,'devis_lot_produit_article.id_fournisseur'
			,'devis_lot_produit_article.prix_achat'
			,'devis_lot_produit_article.conditionnement'
			,'devis_lot_produit_article.id_marge'
			,'devis_lot_produit_article.visible'
		);

		$this->colonnes['primary'] = array(
			"quantite"
			,"article"
			,"unite"
			,"prix_achat"
			,"conditionnement"
			,"visible"
		);

		$this->fieldstructure();
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->field_nom = "article";
		$this->addPrivilege("selectFromDevis");

		$this->colonnes['ligne'] =  array(
			"devis_lot_produit_article.quantite"
			, "devis_lot_produit_article.article"
			, "devis_lot_produit_article.unite"
			, "devis_lot_produit_article.id_fournisseur"
			, "devis_lot_produit_article.prix_achat"
			, "devis_lot_produit_article.conditionnement"
			, "devis_lot_produit_article.id_marge"
			, "devis_lot_produit_article.visible"
			, "devis_lot_produit_article.id_article"
			, "devis_lot_produit_article.id_devis_lot_produit"
			, "marge.taux"
		);
	}

	/**
	* Retourne les devis_lot_produit_article d'un devis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param Objet querier
	*/
	 public function selectFromDevis($post,$s) {
	 	if (!$post["id_devis"]) return;

		$q = ATF::_s("pager")->create($post["pager"],NULL,true);
		$q->reset('where')
			->from("devis_lot_produit_article","id_devis_lot_produit","devis_lot_produit","id_devis_lot_produit")
			->from("devis_lot_produit","id_devis_lot","devis_lot","id_devis_lot")
			->from("devis_lot_produit_article","id_marge","marge","id_marge")
			->addField("marge.taux","taux")
			->where("devis_lot.id_devis",$this->decryptId($post["id_devis"]));

		$post["function"]="selectFromDevisAfter";
		$res = $this->extJSgsa($post,$s);
		foreach ($res as $key => $value) {
			$res[$key]["devis_lot_produit_article.id_marge"] = $res[$key]["marge.taux"];
			$res[$key]["devis_lot_produit_article.id_article"] = $res[$key]["devis_lot_produit_article.article"];
			$res[$key]["devis_lot_produit_article.id_devis_lot_produit"] = $this->decryptId($res[$key]["devis_lot_produit_article.id_devis_lot_produit_fk"]);
			unset($res[$key]["devis_lot_produit_article.id_devis_lot_produit_fk"]);
		}

		return $res;
	}

  	/**
	* Retourne les produits d'un devis pour le grid des devis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
  	public function selectFromDevisAfter() {
		$this->q->reset('field,limit,page,order')->addField(util::keysOrValues($this->colonnes['ligne']))->addOrder("devis_lot_produit_article.id_devis_lot_produit_article","asc");
		return $this->sa();
	}
}