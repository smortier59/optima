<?
/**
* @package Optima
*/
class facture_ligne extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "facture";
		$this->colonnes['fields_column'] = array(
			'facture_ligne.produit'
			,'facture_ligne.quantite'=>array("width"=>50,"align"=>"center")
			,'facture_ligne.ref'=>array("width"=>100,"align"=>"center")
			,'facture_ligne.prix'=>array("width"=>100,"align"=>"right","renderer"=>"money")
			,'facture_ligne.prix_achat'=>array("width"=>100,"align"=>"right","renderer"=>"money")
		);
		$this->colonnes['primary'] = array(
			"id_facture"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::produit()->autocompleteMapping
			))
			,"id_fournisseur"
			,"id_compte_absystech"
		);

		$this->colonnes['bloquees']['insert'] = array('id_facture_ligne','id_commande');
		$this->colonnes['ligne'] =  array(
			"facture_ligne.id_produit"=>array("hidden"=>true)
			,"facture_ligne.produit"=>array("textarea"=>true)
			,"facture_ligne.quantite"
			,"facture_ligne.ref"
			,"facture_ligne.prix"
			,"facture_ligne.id_fournisseur"
			,"facture_ligne.prix_achat"
			,"facture_ligne.id_compte_absystech"
			,"facture_ligne.serial"
		);
		$this->fieldstructure();
		$this->foreign_key['id_fournisseur'] = "societe";
		$this->addPrivilege("update_ligne","update");
		$this->addPrivilege("insert_session","update");
	}

	/**
    * Permet d'avoir les lignes de facture dans l'ordre d'insertion
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	function select_all($order_by=false,$asc='asc',$page=false,$count=false,$parent=false){
		$this->q->reset('limit,page');
		$select_all=parent::select_all($order_by,$asc,$page,$count);
		if($select_all["count"]>0){
			foreach($select_all["data"] as $key=>$item){
				if($item["prix"] && $item["prix_achat"]){
					$select_all["data"][$key]["facture_ligne.marge"]=(($select_all["data"][$key]["facture_ligne.prix"]-$select_all["data"][$key]["facture_ligne.prix_achat"])/$select_all["data"][$key]["facture_ligne.prix"])*100;
					$select_all["data"][$key]["facture_ligne.marge_absolue"]=($select_all["data"][$key]["facture_ligne.prix"]*$select_all["data"][$key]["facture_ligne.quantite"])-($select_all["data"][$key]["facture_ligne.prix_achat"]*$select_all["data"][$key]["facture_ligne.quantite"]);
				}else{
					$select_all["data"][$key]["facture_ligne.marge"] = $select_all["data"][$key]["facture_ligne.marge_absolue"] = 0;
				}

			}
		}
		return $select_all;
	}
};
?>
