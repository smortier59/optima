<?
/** Classe cgl_article
* @package Optima
* @subpackage Cléodis
*/
class cgl_article extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			 'cgl_article.id_cgl_article'			 
			,"cgl_article.numero"
			,'cgl_article.titre'
		);
		
		$this->colonnes["primary"] = array('id_cgl_article','titre','numero');
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>1);

		$this->field_nom = "titre";

		$this->fieldstructure();

		$this->onglets = array(		
			 "cgl_texte"			
		);
	}
};
?>