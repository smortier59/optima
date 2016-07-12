<?
/**
* Classe site_article
* @package Optima
*/
class site_article extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() { 
		parent::__construct();
		$this->table = "site_article";

		$this->colonnes['fields_column'] = array(
			 'site_article.id_site_menu'
			,'site_article.id_parent'
			,'site_article.titre'			
			,'site_article.position'			
            ,'site_article.visible'
		);


		$this->field_nom = "titre";
		$this->fieldstructure();	
		$this->foreign_key["id_parent"] = "site_article";

		$this->onglets = array("site_article_contenu");

	}


}