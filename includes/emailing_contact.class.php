<?
/** 
* Classe emailing_contact, gère les listes de diffusion
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
*/
class emailing_contact extends emailing {
	function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(	
			'emailing_contact.id_emailing_source'
			,'emailing_contact.civilite'=>array("width"=>40,"align"=>"center")
			,'emailing_contact.nom'
			,'emailing_contact.prenom'
			,'emailing_contact.societe'
			,'emailing_contact.email'=>array("renderer"=>"email")
			,'emailing_contact.opt_in'=>array("width"=>40,"align"=>"center")
			,'emailing_contact.sollicitation'=>array("width"=>100,"align"=>"center")
			,'emailing_contact.tracking'=>array("width"=>150,"align"=>"center")
			,'emailing_contact.last_tracking'=>array("width"=>150,"align"=>"center")
		);
		$this->colonnes['primary'] = array(
			"nom_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"civilite"=>array("width"=>90)
				,"prenom"
				,"nom"
			))
			,"id_emailing_source"
		);
		
		$this->colonnes['panel']['adresse_complete_fs'] = array(
			"email"						
			,"adresse"=>array("custom"=>true)
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
		); 
		$this->panels['adresse_complete_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);
		
		$this->colonnes['panel']['diverses'] = array(
			"societe"
			,"fonction"
			,"divers_1"
			,"divers_2"
			,"divers_3"
			,"divers_4"
			,"divers_5"
		);
		$this->panels['diverses'] = array("visible"=>false,"collapsible"=>true,'nbCols'=>1,'isSubPanel'=>true);
		
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'adresse_complete_fs')
			,"infos_diverses"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'diverses')
		);
		$this->panels['coordonnees'] = array("visible"=>true);
		
		$this->colonnes['panel']['infosSPM'] = array(
			"sollicitation"
			,"tracking"
			,"last_tracking"
			,"erreur"
			,"opt_in"
		);
		$this->panels['infosSPM'] = array("visible"=>true,'nbCols'=>4);

		$this->colonnes['bloquees']['select'] =  array(
			"adresse_2"
			,"adresse_3"
			,"ville"
			,"cp"
			,"id_pays"
			,"date"
			,"id_owner"
		);

		$this->colonnes['bloquees']['insert'] = 
		$this->colonnes['bloquees']['update'] =  array(
			"date"
			,"id_owner"
			,"sollicitation"
			,"tracking"
			,"last_tracking"
			,"erreur"
		);

		$this->fieldstructure();
		$this->field_nom = "email";
		$this->fieldPluginsSpmInfo = array(
			"civilite"
			,"nom"
			,"prenom"
			,"societe"
			,"adresse"
			,"email"
			,"divers_1"
			,"divers_2"
			,"divers_3"
			,"divers_4"
			,"divers_5"
		);
		$this->addPrivilege("getColsSpmPlugins");
		$this->formExt=true;
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Contacts_d%27emailing";

		// Pour l'import, la clé unique différente de la clé primaire mysql :
        $this->champs_unique = "email";
	}
		
	/**
	* Renvoi les colonnes de la table utilisable avec le plugin Ext JS SpeedMail pour ajouter des infos personnalisé.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 29-09-2010
	* @return array Liste des champs
	*/
	function getColsSpmPlugins() {
		$a = array();
		foreach ($this->fieldPluginsSpmInfo as $k=>$i) {
			$a[] = array(
				"label"=>ATF::$usr->trans($i,$this->table)
				,"value"=>$i
			);
		}
		ATF::$cr->rm("top");
		ATF::$cr->block("generationTime");
		return $a;

	}
	
	/**
	* Désinscrit un contact des mailings
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	* @param int id ID du contact d'emailing passé soit en clair, soit en cryptage simple MD5. NE SURTOUT PAS PASSE UN ID CRYPTE PAR AES.
	* @return bool Cf update
	*/
	function unregister($id) {
		$this->q->reset();
		if (strlen($id)==32) {
			$this->q->Where("MD5(id_emailing_contact)",$id);
		} else {
			$this->q->Where("id_emailing_contact",$id);
		}
		$this->q->addValues(array("opt_in"=>"non"));
		return ATF::db()->update($this);
	}
	
	/**
	* Export brut surchargé pour éviter l'utilisation s'il n'y a aucun contacts dans la source
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 10-10-2012
	*/
	function export_brut(&$infos,&$s) {
		// Identification de l'ID de la source
		if ($infos['onglet'] && preg_match("/gsa_emailing_source/",$infos['onglet'])) {
			$tmp = explode("_",$infos['onglet']);
			$id = array_pop($tmp);
			$id = $this->decryptId($id);
			$this->q->reset()->where('id_emailing_source',$id)->setCountOnly();

			if (!$this->sa()) {
				throw new error(ATF::$usr->trans("aucun_enregistrement_pour_cette_source",$this->table));	
			}
		}
		parent::export_brut($infos,$s);
	}
	
	
	public function contactsBySource($id_es,$countOnly=false) {
		if (!$id_es) return false;
		$this->q->reset()->where("id_emailing_source",ATF::emailing_source()->decryptId($id_es));
		if ($countOnly) {
			$this->q->setCountOnly();
		}
		return $this->sa();
	}
		
};
?>