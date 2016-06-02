<?
/**
* Classe Societé du GIE
* Cet objet permet de gérer les entités au sein du CRM
* @package Optima
*/
class gie_societe extends classes_optima {
	/**
	* Constructeur par défaut
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;

		$this->colonnes['fields_column'] = array(
			'gie_societe.codename'
			,'gie_societe.societe'
			,'gie_societe.nom_commercial'
			,'gie_societe.siren'
			,'gie_societe.tel' => array("tel"=>true)
			,'gie_societe.fax' => array("tel"=>true)
			,'gie_societe.email'
			,'gie_societe.ville'
		);

		// Panel prinicpal
		$this->colonnes['primary'] = array(
			"codename"
			,"nom"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"societe"
				,"nom_commercial"
			))
			,"sirens"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"siren"
				,"siret"
			))
			,"etat"
			,"relation"
		);

		// Adresse
		$this->colonnes['panel']['adresse_complete_fs'] = array(
			"adresse"
			,"adresse_2"
			,"adresse_3"
			,"cp_ville"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"cp"
				,"ville"
			))
			,"id_pays"
		);
		$this->panels['adresse_complete_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);

		// Coordonnées supplémentaires
		$this->colonnes['panel']['coordonnees_supplementaires_fs'] = array(
			"tel_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"tel"=>array("renderer"=>"tel","custom"=>true,"tel"=>true)
				,"fax"=>array("renderer"=>"tel")
			))
			,"email"=>array("quick_update"=>true)
			,"web"
			,"coordonnees_gps"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"latitude"
				,"longitude"
			))
		);
		$this->panels['coordonnees_supplementaires_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);

		// Panel prinicpal des coordonnées
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'adresse_complete_fs')
			,"coordonnees_supplementaires"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'coordonnees_supplementaires_fs')
		);
		$this->panels['coordonnees'] = array("visible"=>true);

		// Structure et secteur
		$this->colonnes['panel']['structure_secteur_fs'] = array(
			"structure_societe"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"structure"
				,"activite"
				  ,"naf"
			))
			,"information_financiere"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"capital"
				,"ca"
			))
			,"taille"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"effectif"
				,"nb_employe"
			))
		);
		$this->panels['structure_secteur_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);

		// Panel prinicpal des catactéristiques
		$this->colonnes['panel']['caracteristiques'] = array(	
			"structure_secteur"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'structure_secteur_fs')
			,"fournisseur"
			,"partenaire"
		);

		$this->colonnes['bloquees']['recherche'] = array("id_".$this->table);

		$this->fieldstructure();

		$this->gmap = true;
		$this->field_nom = "societe";
		$this->quick_action['select_all'][] = "geolocalisation";
		$this->quick_action['select'][] = "geolocalisation";
		$this->onglets = array('gie_contact');

		$this->no_insert = true;
		$this->no_delete = true;
		$this->no_update = true;
	}

	/**
	* Méthode spéciale par défaut "saCustom"
	* Appel la méthode de classe particulière à utiliser,  si $method =flase on utilise select_all
	* Utilisation dans generic_select_all
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $s : la session
	* @param string $method : methode de classe particulière à utiliser
	* @return array Résultat de la requête
	public function select_data(&$s,$method=false){
		if (!$method) {
			$method="saCustom";
		}
		return parent::select_data($s,$method);
	}
	*/

	/**
	* Surcharge du select-All
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function saCustom(){
		$this->q->addField("societe.etat");
		return $this->select_all();
	}
	*/
}