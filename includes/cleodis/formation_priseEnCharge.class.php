<?
/** 
* Classe formation_priseEnCharge
* @package Optima
*/
class formation_priseEnCharge extends classes_optima {

	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();
		$this->table = "formation_priseEnCharge";

		$this->colonnes['fields_column'] = array( 
			 'formation_priseEnCharge.ref'
			,'formation_priseEnCharge.id_formation_devis'
			,'formation_priseEnCharge.opca'			
			,'formation_priseEnCharge.date_envoi'=>array("renderer"=>"updateDate","width"=>170)
			,'formation_priseEnCharge.date_retour'=>array("renderer"=>"updateDate","width"=>170)
			,'formation_priseEnCharge.etat'=>array("renderer"=>"etat","width"=>30)
			,'formation_priseEnCharge.montant_demande'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")
			,'formation_priseEnCharge.montant_accorde'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>50,"renderer"=>"uploadFile")
		);

		// Panel prinicpal
		$this->colonnes['primary'] = array(
			 "ref"
			,'id_formation_devis'
			,'opca'			
			,'date_envoi'
			,'etat'
			,'montant_demande'
			,"subro_client"
		);

		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['clone'] =  array_merge(array('montant_accorde',"date_retour","ref"));


		$this->fieldstructure();

		$this->field_nom = "ref";

		$this->foreign_key["opca"] = "societe";

		$this->files["fichier_joint"]  = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->addPrivilege("uploadFile","update");
	}


	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field){		
		switch ($field) {				
			case "date_envoi" :
				return date("Y-m-d");
		}
		return parent::default_value($field);
	}

	/** 
	* Surcharge de delete afin de supprimer la priseEnCharge et modifier l'état du devis
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		if (is_numeric($infos) || is_string($infos)) {
			$id=$this->decryptId($infos);
			$priseEnCharge=$this->select($id);

			ATF::formation_devis()->u(array("id_formation_devis"=>$priseEnCharge["id_formation_devis"], "etat"=>"attente"));
			$this->d($id);


		}elseif (is_array($infos) && $infos) {
			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files,$cadre_refreshed);
			}
		}
	}
	

};
