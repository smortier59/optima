<?
require_once dirname(__FILE__)."/../societe.class.php";
/**
* @package Optima
* @subpackage AbsysTech
*/
class societe_boisethome extends societe {
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
		);

		$this->colonnes['primary']["Contact"] = array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"Nom"=>array('null'=>true,"custom"=>true,"xtype"=>"textfield", "width"=>250),
				"Prenom"=>array('null'=>true,"custom"=>true,"xtype"=>"textfield", "width"=>250)
			)
		);

		$this->panels['affacturage_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);
		$this->colonnes['panel']['coordonnees']["affacturage"] = array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'affacturage_fs');

		$this->colonnes["speed_insert"] = array(
			'societe'
		);

		$this->fieldstructure();
		// champ parc remplacer par stock
		$this->onglets = array(
			'contact'=>array('opened'=>true)
			,'suivi'=>array('opened'=>true)
			,'affaire'=>array('opened'=>true)
			,'devis'
			,'facture'
		);

		$this->colonnes['bloquees']['select'] =   array("divers_5","Contact");
		$this->colonnes['bloquees']['update'] = array("Contact");

		$this->quick_action['select']['atcard'] = array('privilege'=>'export');
		$this->quick_action['select_all']['atcardImport'] = array('privilege'=>'import');

		$this->addPrivilege('rapprocher');
		$this->addPrivilege('updateRapprocher');
		$this->addPrivilege("atcard","export");
		$this->addPrivilege("atcardImport","import");
		$this->addPrivilege("autocompleteOnlyActive");
		$this->addPrivilege("add_ticket","insert");

		$this->selectExtjs=true;
	}

	/**
	* Surcharge de l'insertion pour les socits
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Les informations  insrer
	* @param array $s la session
	* @param array $files Les fichiers uploads ventuels
	* @param array $cadre_refreshed Le cadre refreshed utilis pour le rafraichissement ajax
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		$infos["divers_5"]=substr(md5(time()),0,4); // Mot de passe hotline
		$infos["mdp_client"]=util::generateRandWord(9);
		$infos["mdp_absystech"]=util::generateRandWord(9);

		ATF::db($this->db)->begin_transaction();

		if($infos["Nom"] && $infos["Prenom"]){
			$Contact = array("nom" =>$infos["Nom"], "prenom" => $infos["Prenom"]);
		}
		unset($infos["Nom"],$infos["Prenom"]);

		try {
			//Unset du crédit pour les duplicates
			unset($infos["credits"]);
			$id_societe = parent::insert($infos,$s,$files,$cadre_refreshed);

			if($Contact){
				$Contact["id_societe"] = $id_societe;
				ATF::contact()->insert($Contact);
			}

			if(ATF::famille()->nom($infos["id_famille"])=="Particulier"){
				$contact=array(
					"civilite"=>NULL,
					"nom"=>$infos["societe"],
					"etat"=>$infos["etat"],
					"id_societe"=>$id_societe,
					"id_owner"=>$infos["id_owner"],
					"adresse"=>$infos["adresse"],
					"adresse_2"=>$infos["adresse_2"],
					"adresse_3"=>$infos["adresse_3"],
					"cp"=>$infos["cp"],
					"ville"=>$infos["ville"],
					"id_pays"=>$infos["id_pays"],
					"tel"=>$infos["tel"],
					"fax"=>$infos["fax"],
					"email"=>$infos["email"],
					"cle_externe"=>$infos["cle_externe"]
				);

				ATF::contact()->insert($contact,$s);

				$this->redirection("select",$id_societe);
			}
		} catch (error $e) {
			ATF::db($this->db)->rollback_transaction();	
			throw new error(ATF::$usr->trans("probleme_insertion",$this->table)." => ".$e->getMessage(),$e->getCode());
		}

		ATF::db($this->db)->commit_transaction();

		return $id_societe;
	}

}