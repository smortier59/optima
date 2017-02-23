<?
/**
* @package Optima
*/
class termes extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('termes.termes');
		$this->colonnes['primary']=array('termes');

		$this->fieldstructure();
	}

	/**
	* Autocomplete sur les termes pour voir toujours TOUS les termes sans pagination
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
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
			->addOrder("termes")
			->setPage(0);
		return parent::autocomplete($infos,false);
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion termes
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_termes")->addField("termes");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}
		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}
};
?>