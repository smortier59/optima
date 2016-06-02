<?
/** Classe audit
* @package Optima
* @subpackage Cléodis
*/
class audit extends classes_optima {	
};

class audit_cap extends audit {

	function __construct($table_or_id) {
		$this->table ="audit";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			 'audit.ref'
			,'audit.id_user'
			,'audit.type'
			,'audit.etat'
			,"audit.id_societe"	
			,"audit.date"		
			,'auditA4'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>50,"align"=>"center")
			,'audit_etendre'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"auditExpand","width"=>50)
			,'perdu'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"auditPerdu","width"=>50)	
			,'retourBPA'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile","width"=>70)
		);
		
		$this->colonnes['primary'] = array(		 
			 'ref'
			,"id_affaire"
			,'id_societe'
			,'id_user'
			,'type'
			,'etat'
			,"date"
		);

		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['clone'] =  
		$this->colonnes['bloquees']['update'] =  array_merge(array('ref','etat','id_user',"id_affaire"));

		$this->fieldstructure();		
		$this->files["auditA4"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"force_generate"=>true);
		$this->files["retourBPA"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->addPrivilege("perdu","update");
	}



	/** 
	* Impossible de modifier un audit qui n'est pas en attente
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id
	* @return boolean 
	*/
	public function can_update($id,$infos=false){
		if($this->select($id,"etat")=="attente"){
			return true;
		}else{
			throw new error("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il n'est plus en '".ATF::$usr->trans("attente")."'",892);
			return false; 
		}
	}
	/** 
	* Impossible de supprimer un audit
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id
	* @return boolean 
	*/
	public function can_delete($id,$infos=false){
		return false; 
	}

	
	/** 
	* Surcharge de l'insert afin d'insérer les lignes de audit de créer l'affaire si elle n'existe pas
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){	$preview=$infos["preview"];
		}else{	$preview=false;	}

		if(isset($infos["tu"])){ $tu = true; }else{ $tu = false; }

		
		$this->infoCollapse($infos);

		ATF::db($this->db)->begin_transaction();
		
		if(!$infos["ref"]) $infos["ref"]=$this->getRef();
		if(!$infos["date"]) $infos["date"]=date("Y-m-d");

		if( !$infos["id_user"] ) $infos["id_user"] = ATF::$usr->get('id_user');

		$affaire = array("ref"=>$infos["ref"],
						"date"=>$infos["date"],
						"id_societe"=>$infos["id_societe"]
						);

		$infos["id_affaire"]=ATF::affaire_cap()->i($affaire,$s);

		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);		
		
		if($preview){
			if(!$tu) $this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			if(!$tu) $this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base		

			ATF::db($this->db)->commit_transaction();
		}
		if(is_array($cadre_refreshed)){	ATF::affaire()->redirection("select",$infos["id_affaire"]);	}
		return $last_id;
	}


	/**
    * Retourne la ref d'une affaire
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_parent
	* @return string ref
    */
	function getRef($date){
		$prefix=date("ym",strtotime($date));
		$this->q->reset()
				->addCondition("ref",$prefix."%","AND",false,"LIKE")
				->addCondition("LENGTH(`ref`)",3,"AND",false,">")
				->addField('SUBSTRING(`ref`,5)+1',"max_ref")
				->addOrder('ref',"DESC")
				->setDimension("row")
				->setLimit(1);
	
		$nb=$this->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="001";
		}
		return $prefix.$suffix;
	}



	/** 
	* Méthode permettant de passer l'état d'un audit et d'une affaire à perdu
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function perdu($infos,&$s,$files=NULL,&$cadre_refreshed){
		$audit=$this->select($infos["id_audit"]);

		if($audit["etat"]!="signe"){
			ATF::db($this->db)->begin_transaction();
			
			$this->u(array("id_audit"=>$audit["id_audit"],"etat"=>"perdu"),$s);
						
			ATF::db($this->db)->commit_transaction();

			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_audit_perdu"),array("record"=>$this->nom($infos["id_audit"])))
				,ATF::$usr->trans("notice_success_title")
			);	
		
			$this->redirection("select_all",NULL,"audit.html");
			return true; 
		}else{	
			throw new error("Impossible de passer un audit gagnée en 'perdu'",899);
		}
	}


};