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


	public function _GET($get, $post){
		// au cas ou il y aurait un changement de format d'id transmis
		$id_affaire =  strlen($get["id_affaire"]) === 32 ?  ATF::affaire()->decryptId($get["id_affaire"]) : $get['id_affaire'];
		ATF::affaire_etat()
			->q
			->reset()
			->addJointure("affaire_etat","id_user","user","id_user") //rajout d'une jointure pour récupérer le login
			->where("id_affaire", $id_affaire)
			->addOrder("affaire_etat.date","desc");
		$ret = ATF::affaire_etat()->select_all();
		return $ret?$ret:false;
	}
}