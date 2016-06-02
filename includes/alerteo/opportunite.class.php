<?
/**
* @package Optima
* @subpackage Alerteo
*/
class opportunite extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'opportunite.id_societe'
			,'opportunite.id_owner'
			,'opportunite.opportunite'
			,'opportunite.forecast'
			,'opportunite.date_echeance'
			,'opportunite.nb_traks'
			,'opportunite.ca'=>array("aggregate"=>array("avg","min","max","sum"/*,"stddev","variance"*/),"align"=>"right","suffix"=>"K€","type"=>"decimal")
			,'ca_pondere'=>array("custom"=>true,"aggregate"=>array("avg","min","max","sum"/*,"stddev","variance"*/),"align"=>"right","suffix"=>"K€","type"=>"decimal")
		);
		$this->colonnes['primary'] = array(
			'id_societe'
			,'id_owner'
		);
		$this->colonnes['panel']['detail'] = array(
			'opportunite'
			,'note'
		);
		$this->colonnes['panel']['chiffres'] = array(
			"ca"
			,"nb_traks"
			,"forecast"=>array("data"=>array("0","20","40","60","80"),"xtype"=>"combo")
			,"id_societe_partenaire"
			,"id_domaine"
			,"id_famille"
		);
		$this->colonnes['panel']['planning'] = array(
			"date"
			,"date_offre"
			,"date_echeance"
		);
		$this->colonnes['bloquees']['insert'] = 
		$this->colonnes['bloquees']['update'] = array(
			"date"
			,"id_user"
		);
		$this->fieldstructure();
		$this->panels['detail'] = array("visible"=>true,"nbCols"=>1);
		$this->panels['chiffres'] = array("visible"=>true);
		$this->panels['planning'] = array("visible"=>true);
		$this->foreign_key['id_societe_partenaire'] = "societe";
		$this->onglets = array('suivi');
	}

	/**
    * Calcul du CA pondéré
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addField("(opportunite.ca*opportunite.forecast/100)","ca_pondere");
		return parent::select_all($order_by,$asc,$page,$count);	
	}

	/**
    * Le créateur est l'utilisateur loggué
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */ 
	public function insert($infos,&$s,$files,&$cr){
		$this->infoCollapse($infos);
		if (!$infos['id_user'] && is_array($s) && isset(ATF::$usr)) {
			$infos['id_user'] = ATF::$usr->getID();
		}
		return parent::insert($infos,$s,$files,$cr);	
	}
	
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field){
		switch ($field) {
			case "forecast":
				return "0";
			case "id_owner":
				return ATF::$usr->getID();
			default:
				return parent::default_value($field);
		}
	
	}	 
};
?>