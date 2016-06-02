<?
class produit_article extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->controlled_by = "produit";
		$this->table = "produit_article";
		$this->colonnes['fields_column'] = array(
			 'produit_article.id_article'
			,'produit_article.id_produit'
			,'produit_article.quantite'=>array("width"=>50,"align"=>"center")
			,"prix_achat"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,"total"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
		);

		$this->colonnes['primary'] = array(
			"id_produit"
			,"id_article"=>array("autocomplete"=>array(
				"mapping"=>ATF::article()->autocompleteMapping
			))
		);

		$this->colonnes['bloquees']['insert'] =  array('id_produit_article','id_produit');

		$this->fieldstructure();
		$this->field_nom = "id_article";
	}

	/**
    * Permet d'avoir les lignes de devis dans l'ordre d'insertion
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	function select_all($order_by=false,$asc='desc',$page=false,$count=false,$parent=false){
		$asc="asc";
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		$this->q
			->addJointure("produit_article","id_article","article","id_article")
			->addField("article.prix_achat","prix_achat")
			->addField("(produit_article.quantite*article.prix_achat)","total");
		$select_all=parent::select_all($order_by,$asc,$page,$count);
		return $select_all;
	}
}