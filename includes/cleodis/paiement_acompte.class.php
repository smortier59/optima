<?
/**
* @package Optima
*/
class paiement_acompte extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('paiement_acompte.paiement_acompte');
		$this->colonnes['primary']=array('paiement_acompte');
		
		$this->fieldstructure();
	}
	
	/**
	* Autocomplete sur les paiement_acompte pour voir toujours TOUS les paiement_acompte sans pagination
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
			->addOrder("paiement_acompte")
			->setPage(0);				
		return parent::autocomplete($infos,false);
	}
};
?>