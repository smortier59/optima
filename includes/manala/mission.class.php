<?
/** Classe mission
* @package Optima MANALA
*/
class mission extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes["fields_column"] = array(
			'mission.ref'
			,'mission.mission'
			,'mission.etat'=>array('renderer'=>'etat',"width"=>50)
			,'mission.date_debut'
			,'mission.date_fin'
			,'mission.id_societe'
			,'mission.lieu'
			,'mission.nb_personnel'=>array("width"=>50)
			,'total_personnel'=>array("custom"=>true,"width"=>50)
			,'mission.actions'=>array('renderer'=>'actionsMission',"width"=>80,"custom"=>true,"nosort"=>true,"align"=>"center")
		);

		$this->colonnes['primary'] = array(
			"mission"
			,"date_debut"
			,"id_societe"
			,"date_fin"
			,"id_contact"=>array(
				"obligatoire"=>true,
				"autocomplete"=>array(
					"function"=>"autocompleteAvecMail"
					,"mapping"=>array(
						array('name'=> 'email', 'mapping'=> 0)
						,array('name'=>'id', 'mapping'=> 1)
						,array('name'=> 'nom', 'mapping'=> 2)
						,array('name'=> 'detail', 'mapping'=> 3, 'type'=>'string' )
						,array('name'=> 'nomBrut', 'mapping'=> 'raw_2')
						,array('name'=>'civilite', 'mapping'=> "civilite")
					)
				)
			)
			,"lieu"
			,"description"=>array("xtype"=>"textarea")
			,"etat"
			,"indication_personnel"=>array("xtype"=>"textarea")
			,"detail_horaire"=>array("xtype"=>"textarea")
			,"specific_doc"=>array("data"=>array("attestationSecurite"=>ATF::$usr->trans("attestationSecurite"),"attestationEmploi"=>ATF::$usr->trans("attestationEmploi")))
			,"nb_personnel"
		);	
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>2);
		
		$this->colonnes['panel']['lignes'] = array(
			"personnel"=>array("custom"=>true)
		);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);

		$this->colonnes['panel']['documents'] = array(
			"fichier"=>array("custom"=>true)
		);
		$this->panels['documents'] = array("visible"=>true, 'nbCols'=>1);

		$this->colonnes['bloquees']['select'] = array('personnel','ref');
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['update'] = array('ref','etat');


		$this->files["DUE"] = array("type"=>"pdf","preview"=>true,"no_upload"=>false,"no_generate"=>true);

		$this->fieldstructure();

		$this->field_nom = "[%ref%] %mission%";

		$this->onglets = array(
			'mission_ligne',
			'mission_ged'
		);
		
		$this->addPrivilege("validate","update");
	}

	/**
	* Surcharge du select-All
	* @author Quentin JANON <qjanon@absystech.fr>
	**/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			log::logger($i,"qjanon");
			ATF::mission_ligne()->q->reset()->setCountOnly()->where("id_mission",$i['mission.id_mission']);
			$total = ATF::mission_ligne()->select_cell();
			ATF::mission_ligne()->q->where("etat","valide");
			$validP = ATF::mission_ligne()->select_cell();
			$return['data'][$k]['total_personnel'] = $validP."/".$total;
		}
		return $return;
	}

	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$lines = json_decode($infos["values_".$this->table]["personnel"],true);
		$this->infoCollapse($infos);
		$infos['ref'] = $this->getRef($infos);

		$ged = $infos['ged'];
		unset($infos['ged']);

		//Vérification des champs
		$this->check_field($infos);



		if (!$infos['date']) $infos['date'] = date("Y-m-d H:i:s");

		ATF::db($this->db)->begin_transaction();

		$last_id = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		if ($lines) {
			foreach($lines as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("mission_ligne.","",$k_unescape)]=$i;
					unset($item[$k]);
				}

				unset($item["id_mission_ligne"]);
				$item["id_mission"]=$last_id;
				$item["id_personnel"]=$item["id_personnel_fk"];
				unset($item["id_personnel_fk"]);
				$item["index"]=util::extJSEscapeDot($key);

				ATF::mission_ligne()->insert($item,$s);
			}
		}
		foreach ($ged as $k=>$i) {
			ATF::mission_ged()->insert(array('id_ged'=>$i,'id_mission'=>$last_id));
		}


		$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
		ATF::db($this->db)->commit_transaction();

		return $this->cryptId($last_id);			
	}

	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$lines = json_decode($infos["values_".$this->table]["personnel"],true);

		$this->infoCollapse($infos);
		$infos['ref'] = $this->getRef($infos);

		$ged = $infos['ged'];
		unset($infos['ged']);

		ATF::db($this->db)->begin_transaction();

		$last_id = $infos['id_mission'];

		parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
			
		ATF::mission_ligne()->q->reset()->addCondition("id_mission",$this->decryptId($infos["id_mission"]))->where('etat','en_attente');

		$lines_before=ATF::mission_ligne()->select_all();
		foreach($lines_before as $key=>$item){
			ATF::mission_ligne()->delete(array("id"=>$item["id_mission_ligne"]));
		}

		foreach($lines as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("mission_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			$item["id_mission"]=$last_id;
			$item["id_personnel"]=$item["id_personnel_fk"];
			unset($item["id_personnel_fk"]);
			$item["index"]=util::extJSEscapeDot($key);

			ATF::mission_ligne()->insert($item,$s);
		}

		//on supprime tous les destinataires du suivi avant modification
		ATF::mission_ged()->q->reset()->addCondition("id_mission",$this->decryptId($infos["id_mission"]));
		ATF::mission_ged()->delete();
		ATF::mission_ged()->q->reset();

		foreach ($ged as $k=>$i) {
			ATF::mission_ged()->insert(array('id_ged'=>$i,'id_mission'=>$last_id));
		}

		$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
		ATF::db($this->db)->commit_transaction();

		return $this->cryptId($last_id);			
	}

	/**
    * Valide une mission
 	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function validate($infos,&$s,$files=NULL,&$cadre_refreshed){
		if (!$infos['id']) return false;
		
		$mission = ATF::mission()->select($infos['id']);

		ATF::db($this->db)->begin_transaction();
		// Modification de l'état de la mission
		$toUpdate = array("id_mission"=>$mission['id_mission'],"etat"=>"validee");
		parent::update($toUpdate);
		ATF::$msg->addNotice(ATF::$usr->trans("mission_valide",$this->table));

		// Génération des contrat de travail
		ATF::mission_ligne()->q->reset()->where("id_mission",$mission['id_mission']);
		$personnel = ATF::mission_ligne()->sa();
		foreach ($personnel as $k=>$i) {
			if ($i['etat']=="en_attente") {
				$i['etat'] = "annule";
				ATF::mission_ligne()->u($i);
			}
		}

		// Verification de la présence de personnel
		ATF::mission_ligne()->q->reset()->where("id_mission",$mission['id_mission'])->where('etat','valide')->setCount();
		$personnel = ATF::mission_ligne()->sa();
		if (!$personnel['count']) {
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Impossible de valider une mission sans personnel validé.");
		}
		// Envoi des mails au personnels valide
		foreach ($personnel['data'] as $k=>$i) {	
			unset($mail);
			$email = ATF::personnel()->select($i['id_personnel'],"email");
			if (!$email) continue;
			$mail["objet"] = "Recrutement validé pour la mission : ".$mission['mission'];
			$mail["from"] = "Valérie DEBURCQ / Manala <vd.manala@outlook.fr>";
			$mail["html"] = false;
			$mail["template"] = 'mission';
			$mail["texte"] = "";
			$mail["recipient"] = $email;

			$mail["nom"] = ATF::personnel()->nom($i['id_personnel']);

			$mail['mission'] = $mission;

			// Pièce jointe description dans le mail !
			$ct = ATF::mission_ligne()->filepath($i['id_mission_ligne'],"contratTravail");
			if (file_exists($ct)) {
				$mail['files']['Contrat de travail'] = "imprimez le et signez le pour le présenter à votre arrivée ! SURTOUT PRENEZ AVEC VOUS VOTRE PIECE D'IDENTITE";
			}
			$due = ATF::mission_ligne()->filepath($i['id_mission_ligne'],"due");
			if (file_exists($due)) {
				$mail['files']["Déclaration Unique d'Embauche"] = "";
			}

			if ($ged = ATF::mission_ged()->ss('id_mission',$mission["id_mission"])) {
				foreach ($ged as $doc) {
					$el = ATF::ged()->select($doc['id_ged']);
					$fp = ATF::ged()->filepath($el['id_ged'],"fichier");
					if (file_exists($fp)) {
						$mail['files'][$el['ged']] = $el['description'];
					}
				}
			}
			foreach (explode(",",$mission['specific_doc']) as $doc) {
				$mail['files'][ATF::$usr->trans($doc,$this->table)] = "";
			}

			$m = new mail($mail);
			// Pièce jointe du mail !
			if (file_exists($ct)) {
				$m->addFile($ct,"Contrat_de_travail_-_".str_replace(" ","_",$mission["mission"]).".pdf",true);
			}
			if (file_exists($due)) {
				$m->addFile($due,"DUE_-_".str_replace(" ","_",$mission["mission"]).".pdf",true);
			}

			if ($ged) {
				foreach ($ged as $doc) {
					$el = ATF::ged()->select($doc['id_ged']);
					$fp = ATF::ged()->filepath($el['id_ged'],"fichier");
					if (file_exists($fp)) {
						$m->addFile($fp,str_replace(" ","_",$el['ged']),true);
					}
				}
			}
			foreach (explode(",",$mission['specific_doc']) as $doc) {
				if (ATF::pdf() instanceof pdf && method_exists(ATF::pdf(),$doc)) {
					$fp = "/tmp/".$i['id_mission_ligne'].".".$doc;
					$data = ATF::pdf()->generic($doc,$i['id_mission_ligne'],true);
					if (file_put_contents($fp,$data)) {
						$m->addFile($fp,$doc.".pdf",true);
					}
					$fpToUnlink[] = $fp;
				}
			}
			$m->send();
		}
		ATF::$msg->addNotice(ATF::$usr->trans("email_send_to_personnel",$this->table));

		ATF::db($this->db)->commit_transaction();

		foreach ($fpToUnlink as $k=>$i) {
			if (file_exists($i)) unlink($i);
		}

		if(is_array($cadre_refreshed)){
			$this->redirection("select",$infos["id"]);
		}		
		return $infos['id'];
	}

	// [CLIENT][JOUR][MOIS][LIEU]
	public function getRef($data) {
		return str_replace(" ","",strtoupper(ATF::societe()->nom($data['id_societe']).date("dm",strtotime($data['date'])).$data['lieu']));
	}

	/**
 	* Retourne true c'est à dire que la modification est possible
 	* @author Quentin JANON <qjanon@absystech.fr>
 	* @return boolean
 	*/
	public function can_update($id,$infos=false){
		if($el=$this->select($id)){			
			if($el["etat"]!="en_attente"){
				throw new errorATF("Il est impossible de modifier une mission qui n'est pas en attente ",892);
			}else{
				return true;
			}
		}else{
			return false;
		}
	}

};
?>