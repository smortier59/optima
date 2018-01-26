<?
/** Classe mission_ligne
* @package Optima MANALA
*/
class mission_ligne extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "mission"; 

		$this->colonnes["fields_column"] = array(
			'mission_ligne.id_mission'
			,'mission_ligne.id_personnel'
			,'mission_ligne.prix'
			,'mission_ligne.panier_repas'
			,'mission_ligne.nb_panier_repas'
			,'mission_ligne.prix_panier_repas'
			,'mission_ligne.defraiement'
			,'mission_ligne.indemnite_defraiement'
			,'mission_ligne.poste'
			,'files'=>array("custom"=>true,"nosort"=>true,"renderer"=>"pdfMission","width"=>90)
			,'contratTravail'=>array("width"=>50,"nosort"=>true,"type"=>"file","custom"=>true)
			,'mission_ligne.note'=>array("renderer"=>"rating","width"=>70)
			,'mission_ligne.etat'=>array('renderer'=>'etat',"width"=>50)
			,'mission_ligne.actions'=>array('renderer'=>'actionsMissionligne',"width"=>60,"custom"=>true,"nosort"=>true,"align"=>"center")
		);

		$this->colonnes['primary'] = array(
			"id_mission"
			,"id_personnel"
			,'note'
			,'commentaire'
		);	
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>2);

		$this->colonnes['panel']['details'] = array(
			"prix"
			,"panier_repas"
			,'nb_panier_repas'
			,'prix_panier_repas'
			,'defraiement'
			,'indemnite_defraiement'
			,'poste'
			,'heure_totale'
		);	
		$this->panels['details'] = array("visible"=>true,'nbCols'=>3);

		
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['update'] = array('etat');

		$this->fieldstructure();

		$this->files["contratTravail"] = array("type"=>"pdf","no_upload"=>true);
		$this->addPrivilege("valid","update");
		$this->addPrivilege("cancel","update");

	}

	/**
    * Valide un personnel pour une mission et genere son contrat de travail
 	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed){
		if (!$infos['id']) return false;
		
		$mission = ATF::mission()->select($this->select($infos['id'],"id_mission"));

		if ($mission['etat']!='en_attente') {
			throw new errorATF("Impossible de changer l'état du personnel sur une mission qui n'est pas en attente.");
		}

		$toUpdate = array("id_mission_ligne"=>$this->decryptId($infos['id']),"etat"=>"valide");
		parent::update($toUpdate);
		ATF::$msg->addNotice(ATF::$usr->trans("personnel_valide",$this->table));

		$infos["filestoattach"] = array("contratTravail"=>"");
		$this->move_files($infos['id'],$s,false,$infos["filestoattach"]);
		ATF::$msg->addNotice(ATF::$usr->trans("contratTravail_genere",$this->table));

		return $infos['id'];
	}

	/*
	 * Annule une mission et detruit les contrats de travail
 	 * @author Quentin JANON <qjanon@absystech.fr>
 	 * @param id
	 * @return TRUE si vrai, sinon FALSE
	 */	
	function cancel($infos) {
		if (!$infos['id']) return false;
		$d = array("id_mission_ligne"=>$this->decryptId($infos['id']),"etat"=>"annule");
		ATF::$msg->addNotice(ATF::$usr->trans("personnel_cancel",$this->table));
		$fp = $this->filepath($this->decryptId($infos['id']),"contratTravail");
		if (file_exists($fp)) {
			unlink($fp);
			ATF::$msg->addNotice(ATF::$usr->trans("contratTravail_delete",$this->table));
		}
		return parent::update($d);
	}

	public function GetNbByPersonnel($id) {
		$this->q->reset()->where('id_personnel',$id)->where('etat','valide')->setCountOnly();
		return $this->sa();
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
 	 * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @return string
    */  
	public function default_value($field){
		$field = str_replace("mission_ligne.","",$field);
		switch ($field) {
			case "poste":
				return "Animatrice";
			break;
			case "prix":
				return 9.81;
			break;
			case "prix_panier_repas":
				return 8.8;
			break;
			default:
				return parent::default_value($field);
		}
	}
	


};
?>