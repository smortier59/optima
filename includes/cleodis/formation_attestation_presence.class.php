<?
/**  
* Classe formation_attestation_presence
* @package Optima
*/
require_once dirname(__FILE__)."/../formation_attestation_presence.class.php";
class formation_attestation_presence_cleodis extends formation_attestation_presence {
	
/** 
	* Constructeur
	*/
	public function __construct() {
		$this->table = "formation_attestation_presence";
		parent::__construct();
		
		$this->colonnes["fields_column"] = array(
			 'formation_attestation_presence.id_contact'
			,'formation_attestation_presence.id_formation_devis'
			,'formation_attestation_presence.id_formation_commande'
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)	
			,'APsigne'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>60)
		);
			
		
		$this->colonnes['primary'] = array(
			 "id_formation_commande"
			,"id_contact"
		);

		$this->fieldstructure();
		
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true, "no_upload"=>true);
		$this->files["fichier_joint"] = array("type"=>"pdf");

		$this->no_insert = true;
		$this->no_delete = true;
		
		$this->no_update_all = false; // Pouvoir modifier massivement				
	}


	/** 
	* Surcharge de l'insert afin de créer le pdf
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		ATF::db($this->db)->begin_transaction();
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
		$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
		ATF::db($this->db)->commit_transaction();
	}

};
class formation_attestation_presence_cleodisbe extends formation_attestation_presence_cleodis { };
class formation_attestation_presence_cap extends formation_attestation_presence_cleodis { };

