<?
/**
* @package Optima
*/
class delai_de_realisation extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->controlled_by = "termes";
		$this->colonnes['fields_column']=array('delai_de_realisation.delai_de_realisation');
		$this->colonnes['primary']=array('delai_de_realisation');

		$this->fieldstructure();
	}

	/**s
	* Autocomplete sur les termes pour voir toujours TOUS les termes sans pagination
	* @author Antoine MAITRE <amaitre@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocomplete($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->setLimit(2000)
			->addOrder("delai_de_realisation")
			->setPage(0);
		return parent::autocomplete($infos,false);
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion delai_de_realisation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_delai_de_realisation")->addField("delai_de_realisation");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}
		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}
};
?>