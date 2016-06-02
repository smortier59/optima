<?
/** Classe base_de_connaissance - Comme son nom l'indique !!
* @package Optima
*/
class base_de_connaissance extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes["fields_column"] = array(
			'base_de_connaissance.base_de_connaissance'
			,'base_de_connaissance.id_user'
			,'base_de_connaissance.date'=>array("width"=>100,"align"=>"center")
			,'base_de_connaissance.last_seen'=>array("width"=>100,"align"=>"center")
			,'base_de_connaissance.frequentation'=>array("width"=>50,"align"=>"center")
			,'fichier_joint'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file")
		);
		$this->colonnes["primary"] = array('base_de_connaissance','id_user','date','last_seen','frequentation');
		
		$this->colonnes['panel']['base_connaissance'] = array("texte");
		
		$this->files["fichier_joint"] = true;
		
		$this->fieldstructure();
	}
};
?>