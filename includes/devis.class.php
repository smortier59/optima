<?
/**
* Classe Devis
* @package Optima
*/
class devis extends classes_optima {
	/**
	* Constructeur
	*/
	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->table = __CLASS__;
	}	

	/**
	* Retourne le CA total en attente de signature
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param boolean $useForecast Si VRAI alors on pondère le CA par le pourcentage de forecast
	* @return int
	*/
	public function getTotalPipe($useForecast=false){
		$this->q->reset()
			->addCondition("devis.etat","attente")
			->addCondition("devis.etat","bloque")
			->setDimension('cell');
		if ($useForecast) {
			$this->q
				->addJointure("devis","id_affaire","affaire","id_affaire")
				->addField("SUM(`devis`.`prix`*`affaire`.`forecast`/100)","nb");
		} else {
			$this->q->addField("SUM(`devis`.`prix`)","nb");
		}
		return parent::select_all();
	}

	/** 
	* Récupère le pipe total pondéré
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return int
	*/
	public function getTotalPipePondere(){
		return $this->getTotalPipe(true);
	}

	/**
	* Retourne les 50 derniers devis ajoutés/modifiés
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*		infos[][date]
	*		infos[][user]
	*		infos[][resume]
	*		infos[][prix]
	*		infos[][etat]
	*/
	public function getRecentForMobile(){
		$this->q->reset()
			->addField("devis.date","date")
			->addField("devis.id_user")
			->addField("devis.resume","resume")
			->addField("devis.prix","prix")
			->addField("devis.etat","etat")
			
			// 30 derniers jours
			->andWhere("devis.date",date("Y-m-d H:i:s",time()-86400*30),"cle",">")
			
			// Sans les révisions obsolète
			->whereIsNotNull("devis.etat");
			
		$return = parent::select_all();
		foreach ($return as $k=>$i) {
			$return[$k]["user"] = $return[$k]["devis.id_user"];
			$return[$k]["etat"] = ATF::$usr->trans($return[$k]["etat"],"devis");
			$return[$k]["humanDate"] = ATF::$usr->date_trans($return[$k]["date"],true,false,true);
		}
		return $return;
	}

	public function insert_devis($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		return parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
	}

};
?>