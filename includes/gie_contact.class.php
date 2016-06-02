<?
/**
* Classe contact
* Cet objet permet de gérer les entités au sein du CRM
* @package Optima
*/
class gie_contact extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			'gie_contact.prenom'
			,'gie_contact.nom'
			,'gie_contact.id_gie_societe'
			,'gie_contact.fonction'
			,'gie_contact.tel' => array("tel"=>true,"renderer"=>"tel","width"=>120)
			,'gie_contact.gsm' => array("tel"=>true,"renderer"=>"tel","width"=>120)
			,'gie_contact.email' => array("renderer"=>"email","width"=>250)
			//,'completer' => array("custom"=>true,"renderer"=>"progress","aggregate"=>array("min","avg"),"width"=>100)
		);

		$this->colonnes['primary'] = array(
			"id_gie_societe"
			,"codename"
			,"nom_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"civilite"=>array("width"=>90)
				,"prenom"
				,"nom"
			))
			,"fonction"
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
			,"tel_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"tel"=>array("quick_update"=>true,"custom"=>true,"tel"=>true)
				,"gsm"=>array("custom"=>true,"tel"=>true)
				,"fax"
			))
			,"email"=>array("quick_update"=>true)
		);
		$this->panels['adresse_complete_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);

		// Disponibilité
		$this->colonnes['panel']['dispo_fs'] = array(
			"disponibilite"/*=>array("xtype"=>"checkboxgroup")*/
			,"autres"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"tel_autres"=>array("quick_update"=>true,"tel"=>true)
				,"adresse_autres"
			))
			,"assistants"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"assistant"
				,"assistant_tel"=>array("tel"=>true)
			))
		);
		$this->panels['dispo_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);

		// Blocs Adresses
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'adresse_complete_fs')
			,"dispo"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'dispo_fs')
		);
		$this->panels['coordonnees'] = array("visible"=>true);

		$this->fieldstructure();
		$this->field_nom = "%civilite% %prenom% %nom%";

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
		$this->q->addField("(IF(LENGTH(gie_contact.prenom)>0,1,0)".
			"+IF(LENGTH(gie_contact.nom)>0,1,0)".
			"+IF(LENGTH(gie_contact.email)>0,1,0)".
			"+IF(LENGTH(gie_contact.tel)>0 || LENGTH(gie_contact.gsm)>0,1,0)".
			"+IF(LENGTH(gie_contact.fonction)>0,1,0)".
			"+IF(LENGTH(gie_contact.anniversaire)>0,1,0))*100/6","completer")
			->addField("gie_contact.etat");
		return parent::select_all();
	}
	*/
}