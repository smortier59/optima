<?
class article_history_boisethome extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->controlled_by = "article";
		$this->colonnes['fields_column'] = array(
			'article_history.date_fin'
			,'article_history.id_user'
			,'article_history.id_article'
			,'article_history.id_fournisseur'
			,'article_history.prix_achat'=>array("aggregate"=>array("min","avg","max"),"align"=>"right")
		);
		$this->table = "article_history";
		$this->field_nom = "id_article";
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->fieldstructure();
	}
}