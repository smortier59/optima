<?
require_once dirname(__FILE__)."/../societe.class.php";
/**
* @package Optima
* @subpackage AbsysTech
*/
class societe_2tmangement extends societe {
	/**
	* Constructeur par défaut
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "societe";

		/*-----------Colonnes Select all par défaut------------------------------------------------*/
		$this->colonnes['fields_column'] = array(
			 'societe.societe'
			,'societe.tel' => array("renderer"=>"tel","width"=>120)
			,'societe.email'=>array("renderer"=>"email","width"=>180)
			,'societe.ville'
			,'credits'=>array("custom"=>true,"width"=>100)
			,'dernierSuivi'=>array("custom"=>true)
			,'completer'=>array('custom'=>true,"renderer"=>"progress","aggregate"=>array("min","avg"),"width"=>100,"align"=>"center")
			,'atcard'=>array("renderer"=>"atcard","width"=>50,'custom'=>true,"nosort"=>true,"align"=>"center")
		);

		$this->colonnes["primary"]["date_fin_contrat_maintenance"] = "";
		$this->colonnes["primary"]["date_fin_option"] = "";

		/*-----------Colonnes bloquées select -----------------------*/
		$this->colonnes['bloquees']['select'] = array(
			'societe.meteo'
		);

		// Adresse de facturation
		array_unshift($this->colonnes['panel']['adresse_facturation_complete_fs'],"facturer_le_siege");

		$this->colonnes['panel']['contrat_maintenance'] = array(
			"est_sous_contrat_maintenance"
			,"commentaire_contrat_maintenance"
			,"option_contrat_maintenance"
			,"id_commercial"
		);
		$this->panels['contrat_maintenance'] = array('nbCols'=>1, "visible"=>true);



		$this->colonnes['panel']['affacturage_fs'] = array(
			"rib_affacturage"
			,"iban_affacturage"
			,"bic_affacturage"
		);
		$this->panels['affacturage_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);
		$this->colonnes['panel']['coordonnees']["affacturage"] = array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'affacturage_fs');

		$this->colonnes['panel']['mdp'] = array(
			"divers_5"
			,"mdp_client"
			,"mdp_absystech"
		);
		$this->panels["mdp"] = array('nbCols'=>3);

		$this->colonnes["speed_insert"] = array(
			'societe'
		);



		$this->foreign_key['id_apporteur_affaire'] = "societe";
		$this->foreign_key["id_commercial"] = "user";

		$this->fieldstructure();



		// champ parc remplacer par stock
		$this->onglets = array(
			'contact'=>array('opened'=>true)
			,'suivi'=>array('opened'=>true)
			,'gestion_ticket'
			,'tache'
			,'hotline'
		);

		$this->colonnes['bloquees']['select'] =   array("divers_5");

		$this->selectExtjs=true;
	}
};

?>
