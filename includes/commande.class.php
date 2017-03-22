<?
/**
* Classe commande
* @package Optima
*/
class commande extends classes_optima {
	/**
	* Constructeur
	*/
	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->table = __CLASS__;
	}


	/**
	* méthode permettant de faire les graphes des diffrents modules, dans statistique
	* @author DEV <dev@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function stats($stats=false,$type=false) {
		//on rcupère la liste des années que l'on ne souhaite pas voir afficher sur les graphes
		//on les incorpore ensuite sur les requêtes adéquates
		$this->q->reset();
		/*foreach(ATF::stats()->liste_annees[$this->table] as $key_list=>$item_list){
			if($item_list)$this->q->addCondition("YEAR(`date`)",$key_list);
		}*/
		ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"date");

		switch ($type) {
			case "CA":
				$this->q->addField("YEAR(`date`)","year")
						->addField("MONTH(`date`)","month")
						->addField("AVG(`prix`)","nb")
						->addGroup("year")
						->addGroup("month");
				$stats['DATA'] = parent::select_all();

				$this->q->reset();
				$this->q->addField("DISTINCT YEAR(`date`)","years");
				$stats['YEARS'] =parent::select_all();

				return parent::stats($stats,$type);

			case "marge":
				$this->q->addField("YEAR(`date`)","year")
						->addField("MONTH(`date`)","month")
						->addField("AVG(`prix`-`prix_achat`)","nb")
						->addGroup("year")
						->addGroup("month");
				$stats['DATA'] = parent::select_all();

				$this->q->reset();
				$this->q->addField("DISTINCT YEAR(`date`)","years");
				$stats['YEARS'] =parent::select_all();

				return parent::stats($stats,$type);

			case "pourcentage":
				$this->q->addField("YEAR(`date`)","year")
						->addField("MONTH(`date`)","month")
						->addField("100*AVG((`prix`-`prix_achat`)/`prix`)","nb")
						->addGroup("year")
						->addGroup("month");
				$stats['DATA'] = parent::select_all();

				$this->q->reset();
				$this->q->addField("DISTINCT YEAR(`date`)","years");
				$stats['YEARS'] =parent::select_all();

				return parent::stats($stats,$type);

			default:
				return parent::stats($stats,$type);
		}
	}

};
?>