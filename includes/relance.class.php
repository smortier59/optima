<?
/**
* @package Optima
*/
class relance extends classes_optima {
	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column'] = array(	
			'relance.id_facture'
			,'relance.date_1'
			,'relance.date_2'
			,'relance.date_demeurre'
			,'relance.date_injonction'
		);		
		$this->fieldstructure();
		$this->files["relance"] = array("type"=>"pdf","no_upload"=>true);
		$this->addPrivilege("generate","update");
	}
	
	/** Permet de relancer une facture (génération d'un pdf)
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	function generate($infos,&$s,$files=NULL,&$cadre_refreshed,$nolog=false){
		if (!$infos['id_facture']) return false;
		else $id = $this->decryptId($infos['id_facture']);
		$facture =  ATF::facture()->select($id);
		$client = ATF::societe()->select($facture["id_societe"]);
		$societe = ATF::societe()->select(ATF::$usr->get('id_societe'));
		
		
		$relance = $this->getEl($id);
		if ($numRelance = $this->getNumeroDeRelance($id)) {
			ATF::db($this->db)->begin_transaction();
			
			if ($numRelance=="first") {
				$relance["id_facture"] = $id;
				$relance["id_relance"] = parent::insert($relance,$s,NULL,$var=NULL,NULL,true); 
			}
			
			$devis = ATF::devis()->select($last_id);
			$ref_devis = $devis["ref"]."-".$devis["revision"];
			$from = ATF::usr()->get("email");
		
			// Pour regénérer le fichier à chaque fois ?
			foreach($this->files as $key=>$item){
				if($infos["filestoattach"][$key]===true){
					$infos["filestoattach"][$key]="";	
				}
			}
			$this->move_files($id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
	
			switch ($numRelance) {
				case "first":
					$relance["date_1"] = date("Y-m-d",time());
					$this->update($relance);
					$texte="Première relance";
					ATF::$msg->addNotice(""	,ATF::$usr->trans("premiere_relance_creer_et_envoyee",$this->table));
				break;
				case "second":
					$relance["date_2"] = date("Y-m-d",time());
					$this->update($relance);
					$texte="Deuxième relance";
					ATF::$msg->addNotice(""	,ATF::$usr->trans("deuxieme_relance_creer_et_envoyee",$this->table));
				break;
				case "third":
					$relance["date_demeurre"] = date("Y-m-d",time());
					$this->update($relance);
					$texte="Mise en demeure";
					ATF::$msg->addNotice(""	,ATF::$usr->trans("mise_en_demeure_creer_et_envoyee",$this->table));
				break;
				case "fourth":
					$relance["date_injonction"] = date("Y-m-d",time());
					$this->update($relance);
					ATF::$msg->addNotice(""	,ATF::$usr->trans("date_injonction_maj",$this->table));
				break;
			}
	
	
			$info_mail["objet"] = "[".$societe["societe"]."] ".$texte." concernant la facture ".$facture["ref"];
			$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
			$info_mail["texte"] = $texte;
			$info_mail["facture"] = $facture;
			$info_mail["template"] = 'relance';
			$id_contact_facturation=ATF::societe()->select($facture["id_societe"],"id_contact_facturation");
			if($id_contact_facturation){
				if(!$info_mail["recipient"]=ATF::contact()->select($id_contact_facturation,"email")){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Il n'y a pas d'email pour ce contact",166);
				}
			}else{
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Il n'y a pas de contact pour cette societe".$facture["id_societe"],167);
			}
			
			//Ajout du fichier de relance
			$path = $this->filepath($id,"relance");		
			$mail = new mail($info_mail);
			$mail->addFile($path,util::removeAccents($texte)."_facture-".$facture["ref"].".pdf",true);						
			//Ajout du fichier de facture
			$pathFacture = ATF::facture()->filepath($id,"fichier_joint");
			if (file_exists($pathFacture)) {
				$mail->addFile($pathFacture,"Facture-".$facture["ref"].".pdf",true);						
			}
			$mail->send();
			
			$info_mail["recipient"] = $from;
			$mail_copie = new mail($info_mail);
			$mail_copie->addFile($path,util::removeAccents($texte)."_facture-".$facture["ref"].".pdf",true);						
			if (file_exists($pathFacture)) {
				$mail_copie->addFile($pathFacture,"Facture-".$facture["ref"].".pdf",true);						
			}
			$mail_copie->send();
			ATF::db($this->db)->commit_transaction();
		} else {
			throw new errorATF(ATF::$usr->trans("cycle_relance_terminé",$this->table),168);
		}
		return $relance["id_relance"];
	}

	/** Renvoi la relance associé a une facture
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id IF d'une facture
	* @return array l'enregistrement de la BDD correspondant à la facture
	*/
	public function getEl($id) {
		$this->q->reset()->where('id_facture',$id)->setDimension('row');
		return $this->sa();
	}
	
	/** Renvoi le numéro de relance afin de pouvoir la créer
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $id_facture ID d'une facture
	* @return string|boolean FALSE si le cycle des relances est déjà terminé, sinon renvoi le numéro de la prochaine relance
	*/
	public function getNumeroDeRelance($id_facture) {
		$el = $this->getEl($id_facture);
		if (!$el) {
			return "first";
		} elseif (!$el['date_2']) {
			return "second";
		} elseif (!$el['date_demeurre']) {
			return "third";
		} elseif (!$el['date_injonction']) {
			return "fourth";
		}
		return false;
	}
	
	
};
?>