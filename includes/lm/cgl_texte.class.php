<?
/** Classe cgl_texte
* @package Optima
* @subpackage Cléodis
*/
class cgl_texte extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			 'cgl_texte.id_cgl_texte'			 
			,"cgl_texte.numero"
			,'cgl_texte.id_cgl_article'
		);
		
		$this->colonnes["primary"] = array('id_cgl_texte','texte','numero');
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>1);

		$this->field_nom = "numero";

		$this->fieldstructure();
	}
};
?>