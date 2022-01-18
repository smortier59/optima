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
		$id_affaire =  ATF::affaire()->decryptId($get["id_affaire"]);
		ATF::affaire_etat()
			->q
			->reset()
			->addJointure("affaire_etat","id_user","user","id_user") //rajout d'une jointure pour récupérer le login
			->where("id_affaire", $id_affaire)
			->addOrder("affaire_etat.date","desc");
		$ret = ATF::affaire_etat()->select_all();
		return $ret?$ret:false;
	}

	/**
	 * [_POST insere un nouvel état de l'affaire]
	 * @param  [type] $get
	 * @param  [type] $post
	 * @return [boolean]
	 */
	public function _POST($get, $post){
		$id_affaire = ATF::affaire()->decryptId($post["id_affaire"]);
		if(!$post["etat"]) $etat = "autre";
		else $etat = $post["etat"];

		$commentaire = json_encode($post);

		ATF::affaire_etat()->insert(array(
			'id_affaire'=>$id_affaire
			,'etat'=>$etat
			,'id_user'=>ATF::$usr->get('id_user')
			,'date'=>date("Y-m-d H:i:s")
			,'commentaire'=>$commentaire
		));

		return true;
	}
}