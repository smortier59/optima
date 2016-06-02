<?
/** Classe site_article
* @package Optima
*/
class site_article extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			 'site_article.id_site_menu'
			,'site_article.titre'
			,"site_article.position"
		);
		
		$this->colonnes["primary"] = array('id_site_menu','titre','position',"visible");
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>1);

		$this->field_nom = "titre";

		$this->fieldstructure();

		$this->onglets = array(		
			 "site_article_contenu"			
		);
	}
};
?>