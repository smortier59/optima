<?
/**  
* Classe rdv
* Cet objet permet de gérer les rendez_vous des adherents
* @package Optima
*/
class rdv extends classes_optima {
	
	/** 
	* Constructeur
	*/
	public function __construct() {	
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(			
			 "rdv.id_adherent"
			,"date_rdv"			
			,"type_rdv"	
			,"objet_rdv"
			,"presence"
			,"rdv.id_user"		
		);
		
		$this->colonnes['primary'] = array(
				"id_adherent"
				,"date_contact"
				,"type_contact"
				,"date_rdv"
				,"type_rdv"
				,"objet_rdv"
				,"id_pole_accueil"
				,"presence"
				,"id_demande_conseil"
				,"id_type_accompagnement"
				,"id_user"
				
		);
		
		$this->colonnes['panel']["com"] = array(
				"commentaire"
		);
		
		$this->colonnes['panel']["procedure"] = array(				
				"commentaire_procedure"
				,"observation_procedure"
				,"procedure_en_cours"
				,"orientation_interne"
				,"orientation"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "orientation_externe"
					,"orient_autre"
				))
				,"action_procedure"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "id_action_propose"
					,"autre_action"
				))
			);
			
		$this->panels['primary'] = array('nbCols'=>3,'visible'=>true);
		$this->panels['com'] = array('nbCols'=>1,'visible'=>false);	
		$this->panels['procedure'] = array('nbCols'=>2,'visible'=>false);	
		
		$this->field_nom = "date_rdv";

		$this->foreign_key["orientation_interne"] = "pole_accueil";
		$this->foreign_key["id_pole_accueil"] = "pole_accueil";
		$this->foreign_key["id_user"] = "user";
		$this->foreign_key["id_adherent"] = "adherent";
		$this->fieldstructure();
		
	}
	
	public function rdv_imminent($date){
			
			$this->q->reset()
				->addField("rdv.id_adherent","id_adherent")				
				->addField("rdv.date_rdv","date_rdv")
				->addField("rdv.type_rdv","type_rdv")
				->addField("DATE_FORMAT(rdv.date_rdv,'%Y-%m-%d %h:%i')","date")
				->addField("CONCAT_WS(' ',adherent.civilite,adherent.prenom,adherent.nom)","adherent")		
				->addField("CONCAT_WS(' ',user.civilite,user.prenom,user.nom)","user")		
				->addJointure('rdv','id_adherent','adherent','id_adherent')
				->addJointure('rdv','id_user','user','id_user')
				->addCondition('rdv.date_rdv',$date,NULL,"sup",'>=')
				->addCondition('rdv.date_rdv',date("Y-m-d h:i:s",strtotime('+2 days',strtotime($date))),NULL,"inf",'<')
				//->addCondition('tache_user.id_user',ATF::$usr->getID())					
				->setCount()
				->addOrder("date_rdv","asc");

		$result = parent::select_all(); 
		ATF::$json->add("totalCount",$result["count"]);
		return $result['data'];		
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){		
		$return = parent::select_all($order_by,$asc,$page,$count);

		//Ajout du Num de dossier pour les exports
		if($return[0]){
			foreach ($return as $key => $value) {
				if($value["rdv.id_adherent_fk"]){ $id_adherent = $value["rdv.id_adherent_fk"]; }else{ $id_adherent = $value["id_adherent"];}			
				$return[$key]["num_dossier"] = ATF::adherent()->select($id_adherent, "num_dossier");
			}	
		}
		return $return;
	}

}