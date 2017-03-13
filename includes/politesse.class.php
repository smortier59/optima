<?
/**
* @package Optima
*/
class politesse extends classes_optima{
	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('politesse.politesse','politesse.type'=>array("width"=>100,"align"=>"center"));

		$this->fieldstructure();

	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion politesse
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_politesse")->addField("politesse");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get["type"]) {
			$this->q->where("type",$get['type']);
		}
		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}
};
?>