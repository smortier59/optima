<?
/** Classe site_article_contenu
* @package Optima
* @subpackage Cléodis
*/
class site_article_contenu extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			 'site_article_contenu.id_site_article',"site_article_contenu.texte"
		);
		
		$this->colonnes["primary"] = array('id_site_article','texte');
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>1);

		$this->fieldstructure();		
	}
};
?>