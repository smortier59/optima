<?
class devis_lot_produit_boisethome extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "devis_lot_produit";
		$this->controlled_by = "devis";
		$this->colonnes['fields_column'] = array(
			 'devis_lot_produit.produit'
			,'devis_lot_produit.id_produit'
			,'devis_lot_produit.description'
			,'devis_lot_produit.quantite'
			,'devis_lot_produit.unite'
			,'devis_lot_produit.lambda'
		);

		$this->colonnes['primary'] = array(
			"produit"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::devis()->autocompleteMapping
			))
			,"description"
			,"unite"
			,"lambda"
		);

		$this->fieldstructure();
		$this->field_nom = "produit";
		$this->onglets = array('devis_lot_produit_article');

		$this->addPrivilege("selectFromDevis");

		$this->colonnes['ligne'] =  array(
			"devis_lot_produit.produit"
			, "devis_lot_produit.quantite"
			, "devis_lot_produit.unite"
			, "devis_lot_produit.description"
			, "devis_lot_produit.lambda"
			, "devis_lot_produit.id_produit"
			, "devis_lot_produit.id_devis_lot"
			, "devis_lot_produit.id_devis_lot_produit"
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
			->from("devis_lot_produit","id_devis_lot","devis_lot","id_devis_lot")
			->where("devis_lot.id_devis",$this->decryptId($post["id_devis"]));

		$post["function"]="selectFromDevisAfter";
		$res = $this->extJSgsa($post,$s);
		foreach ($res as $key => $value) {
			$res[$key]["devis_lot_produit.produit"] = $res[$key]["devis_lot_produit.id_devis_lot_produit"];
			$res[$key]["devis_lot_produit.id_devis_lot_produit"] = $this->decryptId($res[$key]["devis_lot_produit.id_devis_lot_produit_fk"]);
			$res[$key]["devis_lot_produit.id_devis_lot"] = $this->decryptId($res[$key]["devis_lot_produit.id_devis_lot_fk"]);
			$res[$key]["devis_lot_produit.id_produit_fk"] = $this->decryptId($res[$key]["devis_lot_produit.id_produit_fk"]);
			unset($res[$key]["devis_lot_produit.id_devis_lot_produit_fk"],$res[$key]["devis_lot_produit.id_devis_lot_fk"],$res[$key]["devis_lot_produit.id_produit"]);
		}

		return $res;
	}

  	/**
	* Retourne les produits d'un devis pour le grid des devis
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
  	public function selectFromDevisAfter() {
		$this->q->reset('field,limit,page,order')->addField(util::keysOrValues($this->colonnes['ligne']))->addOrder("devis_lot_produit.id_devis_lot_produit","asc");
		return $this->sa();
	}
}