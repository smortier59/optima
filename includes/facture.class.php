<? 
/**
* Classe facture
* Cet objet permet de gérer les factures au sein de la gestion commerciale
* @package Optima
*/
class facture extends classes_optima {
	/** 
	* Retourne le total des factures en attente de paiement
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $annee
	* @param boolean $avant VRAI pour demander toutes les années avant celle du paramètre $annee
	* @return int
	*/
	public function getTotalImpayees($annee=0,$avant=false){
		$this->q->reset()
			->addCondition("etat","impayee")
			->setDimension('cell')
			->addField("SUM(prix)","nb");
		if ($annee) {
			if ($avant) {
				$this->q->addCondition("YEAR(date)",$annee,"AND",false,"<");
			} else {
				$this->q->addCondition("YEAR(date)",$annee);
			}
		}
		return parent::select_all();
	}
		
	/**
	* Manager des webservices
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $post
	*							string	$post[data][action]
	*							int		$post[data][id]
	*/
	public function webservice($post,$session,$files){
		switch ($post["action"]) {
			case "generate":
				if ($post["id"]) {
					// Générer la facture
					$this->generatePDF($post,$session);
					return true;
				}
				break;
		}
	}

};
?>