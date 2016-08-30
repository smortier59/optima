<?
/** Classe document_contrat 
* @package Optima
* @subpackage Cléodis
*/
class document_contrat extends classes_optima {
	function __construct($table_or_id=NULL) {
		$this->table ="document_contrat"; 
		parent::__construct($table_or_id);
		
		$this->colonnes['fields_column'] = array(
			 'document_contrat.document_contrat'					
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile")		
		);

		$this->files["fichier_joint"] = array("type"=>"pdf","no_generate"=>true);

		$this->fieldstructure();	
	}
};
?>