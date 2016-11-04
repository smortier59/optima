<?
/** Classe affaire_etat
* @package Optima
* @subpackage Cléodis
*/
class affaire_etat extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = 'affaire';
		$this->colonnes["fields_column"] = array(	
			 'affaire_etat.date'			 
			,"affaire_etat.id_affaire"
			,"affaire_etat.etat"
			,"affaire_etat.commentaire"
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>50,"align"=>"center")
		);
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->fieldstructure();
	}
}