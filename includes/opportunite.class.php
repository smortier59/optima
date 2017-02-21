<?
/**
* @package Optima
*/
class opportunite extends classes_optima {
	function __construct() { // PHP5 hé
		parent::__construct();
		$this->table = __CLASS__;

		$this->colonnes["fields_column"] = array(
			'opportunite.id_societe'
			,'opportunite.opportunite'
			,'opportunite.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
			,'opportunite.date'=>array("width"=>100,"align"=>"center")
			,'opportunite.id_owner'
		);

		$this->colonnes['primary'] = array(
												'id_societe'
												,'opportunite'
												,'description'
												,'etat'
												,'date'
												,'id_owner'
											);
		$this->colonnes['panel']['detail'] = array(
														"source"
														,"source_detail"
														,"ca"
														,"marge"
														,"echeance"
													);
		$this->fieldstructure();
		$this->panels['detail'] = array("visible"=>true);
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion opportunite
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_opportunite")->addField("opportunite");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get['id_societe']) {
			$this->q->where("id_societe",$get["id_societe"]);
		}
		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}
};
?>