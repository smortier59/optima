<?
/**
 * Classe opportunite
 * @package Optima
 */
require_once dirname(__FILE__)."/../opportunite.class.php";
class opportunite_absystech extends opportunite {
	public function __construct() {
		parent::__construct($table_or_id);

		$this->colonnes['fields_column']["nb_suivi"] = array("custom"=>true,"width"=>60);
		$this->colonnes["fields_column"]["toAffaire"] = array("custom"=>true,"renderer"=>"toAffaire","width"=>60);

		$this->fieldstructure();

		$this->onglets = array("suivi");


		$this->addPrivilege("toAffaire");
	}


	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q->addJointure("opportunite","id_opportunite","suivi","id_opportunite")
				->addField("COUNT(suivi.id_suivi)","nb_suivi")
				->addGroup("opportunite.id_opportunite");

		$return = parent::select_all($order_by,$asc,$page,$count);
		return $return;
	}

	public function toAffaire($infos){
		$id = $this->decryptId($infos["id_opportunite"]);
		$opportunite = $this->select($id);

		$affaire = array("id_societe"=>$opportunite["id_societe"],
						 "affaire"=>$opportunite["opportunite"],
						 "date"=>date("Y-m-d H:i:s"));

		$id_affaire = ATF::affaire()->i($affaire);

		ATF::suivi()->q->reset()->where("suivi.id_opportunite",$id);
		$suivis = ATF::suivi()->select_all();

		foreach ($suivis as $key => $value) {
			ATF::suivi()->u(array("id_suivi"=>$value["suivi.id_suivi"], "id_affaire"=>$id_affaire));
		}

		$this->u(array("id_opportunite"=>$id, "etat"=>"fini"));

		ATF::affaire()->redirection("select",$id_affaire);

		return $id_affaire;
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion opportunite
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("opportunite.id_opportunite","id_opportunite")->addField("opportunite.opportunite","opportunite");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get['id_societe']) {
			$this->q->where("opportunite.id_societe",$get["id_societe"]);
		}

		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}
}


class opportunite_att extends opportunite_absystech { }
class opportunite_atoutcoms extends opportunite_absystech { }

?>