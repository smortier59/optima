<?
/** Classe demande_refi
* @package Optima
* @subpackage Cléodis
*/
class demande_refi extends classes_optima {
	function __construct($table_or_id) {
		$this->table ="demande_refi";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'demande_refi.date'
			,'demande_refi.id_refinanceur'
			,'demande_refi.id_affaire'
			,'demande_refi.id_societe'
			,'demande_refi.loyer_actualise'=>array("aggregate"=>array("min","avg","max","sum"),"renderer"=>"money")
			,'demande_refi.taux'=>array("aggregate"=>array("min","avg","max"),"align"=>"right","suffix"=>"%")
			,'pdf'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>50,"align"=>"center")
			,'retourDR'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile","width"=>70)
			,'demande_refi.etat'=>array("width"=>30,"renderer"=>"etat")
			,'demande_refi.validite_accord'=>array("renderer"=>"updateDate","width"=>170)
			,'demande_refi.date_cession'=>array("renderer"=>"updateDate","width"=>170)
			,'demande_refi.duree_refinancement'
		);

		$this->colonnes['primary'] = array(
			"id_societe"=>array("disabled"=>true)
			,"id_affaire"=>array("disabled"=>true)
			,"id_refinanceur"
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
					)
				)
			)
			,"score"=>array("custom"=>true,"disabled"=>true)
			,"avis_credit"=>array("custom"=>true,"disabled"=>true)
		);
//
//		$this->colonnes['panel']['loyer_lignes'] = array(
//			"loyer"=>array("custom"=>true)
//		);

		// Javascript expérimental
		$javascript = "function (t) {
			ATF.ajax('affaire,getCompteTLoyerActualise.ajax'
				,'id_affaire='+Ext.getCmp('demande_refi[id_affaire]').getValue()+'&taux='+Ext.getCmp('demande_refi[taux]').getValue()+'&date_cession='+Ext.util.Format.date(Ext.getCmp('demande_refi[date_cession]').getValue(), 'Y-m-d')+'&vr='+Ext.getCmp('demande_refi[valeur_residuelle]').getValue()
				,{ onComplete: function (o) {
						Ext.getCmp('demande_refi[loyer_actualise]').setValue(o.result);
					}
				}
			);
		}";

		$this->colonnes['panel']['chiffres'] = array(
			"taux"=>array("formatNumeric"=>true,"xtype"=>"textfield","listeners"=>array("change"=>$javascript))
			,"valeur_residuelle"=>array("formatNumeric"=>true,"xtype"=>"textfield","listeners"=>array("change"=>$javascript))
			,"loyer_actualise"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"pourcentage_materiel"
			,"pourcentage_logiciel"
			,"coefficient"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"encours"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"frais_de_gestion"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']['dates'] = array(
			"date"
			,"reponse"
			,"validite_accord"
			,"date_cession"=>array("listeners"=>array("select"=>$javascript,"change"=>$javascript))
			,"duree_refinancement"
		);

		$this->colonnes['panel']['notes'] = array(
			"description"
			,"marque_materiel"
			,"observations"
		);

		$this->colonnes['panel']['notifie'] = array(
			"suivi_notifie"=>array("custom"=>true)
		);

		$this->colonnes['panel']['statut'] = array(
			"etat"
		);

		$this->field_nom = "description";
		$this->fieldstructure();

		//$this->colonnes['bloquees']['insert'] = array('score',"avis_credit");

		$this->noTruncateSA = true;
		$this->files["pdf"] = array("type"=>"pdf","preview"=>true);
		$this->files["retourDR"] = array("type"=>"pdf","no_upload"=>true,"no_generate"=>true);
		$this->panels['loyer_lignes'] = array('nbCols'=>1);
		$this->panels['chiffres'] = array("visible"=>true, 'nbCols'=>4);
		$this->panels['dates'] = array("visible"=>true, 'nbCols'=>3);
		$this->panels['notes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['statut'] = array("visible"=>true, 'nbCols'=>1);
		$this->no_insert = true;
		$this->selectAllExtjs=true;
	}

	public function uploadFileFromSA(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos['display'] = true;
		$class = ATF::getClass($infos['extAction']);
		if (!$class) return false;
		if (!$infos['id']) return false;
		if (!$files) return false;

		$id = $class->decryptID($infos['id']);

		$id_affaire = $class->select($id, "id_affaire");

		foreach ($files as $k=>$i) {
			if (!$i['size']) return false;
			$id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$id_affaire, "provenance"=>ATF::$usr->trans($class->name(), "module")." ".$k." ref : ".$infos['extAction']." ".$class->select($id,$class->field_nom)));
			$this->store($s,$id,$k,$i);

			copy($class->filepath($id,$k), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));

		}
		ATF::$cr->block('generationTime');
		ATF::$cr->block('top');



		$o = array ('success' => true );
		return json_encode($o);
	}

	/**
	* Surcharge de l'insert afin de modifier l'etat de l'affaire
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$this->infoCollapse($infos);

		$notifie_suivi = $infos["suivi_notifie"];
		unset($infos["suivi_notifie"]);

//*****************************Transaction********************************
		ATF::db($this->db)->begin_transaction();
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"],"etat"=>"demande_refi"));

		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base

			if($infos["etat"] === "passage_comite"){
				$suivi = array(
					"id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$infos['id_societe']
					,"id_affaire"=>$infos['id_affaire']
					,"type_suivi"=>'Refinancement'
					,"texte"=>"La demande de refinancement viens de changer d'état (nouvel état : ".ATF::$usr->trans($infos["etat"],$this->table).")"
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
					,'suivi_notifie'=>$notifie_suivi
				);
				ATF::suivi()->insert($suivi);
			}elseif($infos["etat"] === "accepte"){
				$resp_societe = ATF::societe()->select($infos['id_societe'], "id_owner");

				if($resp_societe == 93){ $suivi_notif = array(93);}
				else{ $suivi_notif = array(93,$resp_societe); }

				$suivi = array(
					"id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$infos['id_societe']
					,"id_affaire"=>$infos['id_affaire']
					,"type_suivi"=>'Refinancement'
					,"texte"=>"La demande de refinancement viens de changer d'état (nouvel état : ".ATF::$usr->trans($infos["etat"],$this->table).")"
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
					,'suivi_notifie'=>$resp_societe
				);
				ATF::suivi()->insert($suivi);
			}



			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}
		return $this->cryptId($last_id);
	}

	/*
	* Surcharge de l'update afin de modifier l'etat de l'affaire
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$this->infoCollapse($infos);
		$infos["filestoattach"]["pdf"]="";
		$notifie_suivi = $infos["suivi_notifie"];
		unset($infos["suivi_notifie"]);
		/*****************************Transaction********************************/
		ATF::db($this->db)->begin_transaction();
		parent::update($infos,$s,$files);

		if($preview){
			$this->move_files($infos["id_demande_refi"],$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($infos["id_demande_refi"]);
		}else{
			$this->move_files($infos["id_demande_refi"],$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base


			if($infos["etat"] === "passage_comite"){
				$suivi = array(
					 "id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$infos['id_societe']
					,"id_affaire"=>$infos['id_affaire']
					,"type_suivi"=>'Refinancement'
					,"texte"=>"La demande de refinancement vient de changer d'état (nouvel état : ".ATF::$usr->trans($infos["etat"],$this->table).")\n par ".ATF::$usr->getNom().".\n Refinanceur : ".ATF::refinanceur()->select($infos["id_refinanceur"] , "refinanceur")
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
					,'no_redirect'=>true
				);
				$id_suivi = ATF::suivi()->insert($suivi);

				$liste_email = "";
				foreach ($notifie_suivi as $key => $value) {
					$email = ATF::user()->select($value,'email');
					$liste_email.=($liste_email?",":"").$email;
				}
				if($liste_email){
					$mail = new mail(array(
							"optima_url"=>ATF::permalink()->getURL(ATF::affaire()->createPermalink($infos['id_affaire'])),
							"recipient"=>$liste_email,
							"objet"=>"Suivi Refinancement de la part de ".ATF::user()->nom(ATF::$usr->getID()),
							"template"=>"suivi",
							"id_user"=>ATF::$usr->getID(),
							"id_affaire"=>$infos['id_affaire'],
							"id_suivi"=>$id_suivi,
							"from"=>ATF::$usr->get('email')));
				}



			}elseif($infos["etat"] === "accepte"){
				$resp_societe = ATF::societe()->select($infos['id_societe'], "id_owner");
				$resp_societe = ATF::user()->select($resp_societe , "email");

				$suivi = array(
					 "id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$infos['id_societe']
					,"id_affaire"=>$infos['id_affaire']
					,"type_suivi"=>'Refinancement'
					,"texte"=>"La demande de refinancement vient de changer d'état (nouvel état : ".ATF::$usr->trans($infos["etat"],$this->table).")\n par ".ATF::$usr->getNom().".\n Refinanceur : ".ATF::refinanceur()->select($infos["id_refinanceur"] , "refinanceur")
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
					,'no_redirect'=>true
				);
				$id_suivi = ATF::suivi()->insert($suivi);

				$mail = new mail(array(
							"optima_url"=>ATF::permalink()->getURL(ATF::affaire()->createPermalink($infos['id_affaire'])),
							"recipient"=>"frederique.randoux@cleodis.com,terence.delattre@cleodis.com,".$resp_societe,
							"objet"=>"Suivi Refinancement de la part de ".ATF::user()->nom(ATF::$usr->getID()),
							"template"=>"suivi",
							"id_user"=>ATF::$usr->getID(),
							"id_affaire"=>$infos['id_affaire'],
							"id_suivi"=>$id_suivi,
							"from"=>ATF::$usr->get('email')));

			}

			if($mail){
				if($mail->send()){
					ATF::$msg->addNotice(ATF::$usr->trans("email_envoye",$this->table));
				}
			}

			ATF::db($this->db)->commit_transaction();
			/****************************************************************************/

			if(is_array($cadre_refreshed)){
				ATF::affaire()->redirection("select",$infos["id_affaire"]);
			}

			$id_demande_refi=$this->decryptId($infos["id_demande_refi"]);
			return $id_demande_refi;
		}

	}


	/**
	* Permet de modifier la date sur un select_all
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateDate($infos){

		if ($infos['value'] == "undefined") $infos["value"] = "";

		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];

		if(array_key_exists("validite_accord",$infosMaj)){
			$infosMaj=$this->updateDateValidite($infosMaj,$infos);
		}elseif(array_key_exists("date_cession",$infosMaj)){
			$infosMaj=$this->updateDateCession($infosMaj,$infos);
		}
		if($this->u($infosMaj)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);

//			$id_affaire=$this->select($infosMaj["id_".$this->table],"id_affaire");
//			ATF::affaire()->redirection("select",$id_affaire);
			return true;
		}else{
			return false;
		}
	}

	/**
	* Permet de modifier l'etat sur une modif de date de validite
	* @param array $infosMaj
	* @param array $infos
	* @return array $infosMaj
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateDateValidite($infosMaj,$infos){
		if($infosMaj[$infos["key"]]){
			$infosMaj["etat"]="valide";
		}else{
			$infosMaj["etat"]="accepte";
		}
		return $infosMaj;
	}

	/**
	* Permet de modifier l'etat sur une modif de date de validite
	* @param array $infosMaj
	* @param array $infos
	* @return array $infosMaj
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateDateCession($infosMaj,$infos){
		if($infosMaj[$infos["key"]]){
			$id_affaire=$this->select($infosMaj["id_".$this->table],"id_affaire");
			ATF::commande()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row");
			$commande=ATF::commande()->sa();
			if(!$commande["date_evolution"]){
				throw new errorATF("Impossible d'insérer date de cession car il n'y a pas de date de fin de contrat",875);
			}

			$datetime1 = new DateTime($infosMaj[$infos["key"]]);
			$datetime2 = new DateTime($commande["date_evolution"]);
			$interval = date_diff($datetime1, $datetime2);

			if($interval->invert>0){
				throw new errorATF("La date de cession date est inférieur à la date de début de contrat",876);
			}

			$m=0;

			if($interval->d > 0){
				$m+=1;
			}

			if($interval->m > 0){
				$m+=$interval->m;
			}

			if($interval->y > 0){
				$m+=($interval->y*12);
			}

			ATF::loyer()->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension("row");
			$loyer=ATF::loyer()->sa();

			if($loyer["frequence_loyer"]=="an"){	$frequence_loyer=12;
			}elseif($loyer["frequence_loyer"]=="semestre"){	$frequence_loyer=6;
			}elseif($loyer["frequence_loyer"]=="trimestre"){ $frequence_loyer=3;
			}elseif($loyer["frequence_loyer"]=="mois"){	$frequence_loyer=1;	}

			$infosMaj["duree_refinancement"]=ceil($m/$frequence_loyer)." ".$loyer["frequence_loyer"]."(s)";
		}else{
			$infosMaj["duree_refinancement"]=NULL;
		}
		return $infosMaj;
	}

	/**
    * Fonction qui permet de savoir s'il y a une demande refi validé pour une affaire
    * @author Mathieu TRIBOUILLARD <qjanon@absystech.fr>
    * @param int $id_affaire
    * @return boolean à true s'il y en a une
    */
	public function existDemandeRefi($id_affaire, $all = true){
		$this->q->reset()->addCondition("id_affaire",$this->decryptId($id_affaire))
						 ->addCondition("etat","valide")
						 ->setCount();


		if(ATF::$codename == "cleodis"){
			$this->q->addCondition("demande_refi.id_refinanceur", 17, "AND", false, "!=");
		}

		if($all){
			$this->q->from("demande_refi", "id_refinanceur", "refinanceur","id_refinanceur")->addCondition("refinanceur.code_refi", "REFACTURATION", "AND", false, "!=");
		}


		$demande_refi=$this->sa();
		if($demande_refi["count"]>0){
			return $demande_refi["data"];
		}else{
			return false;
		}
	}


	/**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field,&$s,&$request){
		if ($id_affaire = ATF::_r('id_affaire')) {
			$affaire=ATF::affaire()->select($id_affaire);
			$societe = $affaire["id_societe"];
			ATF::comite()->q->reset()->where("comite.id_affaire",$affaire['id_affaire'],false,"AND")->where("comite.etat","accepte");
			$comite = ATF::comite()->select_row();

		}elseif(ATF::_r('id_demande_refi')){
			$demande_refi = $this->decryptId(ATF::_r('id_demande_refi'));
			$demande_refi = $this->select($demande_refi);
			$societe = $demande_refi['id_societe'];
		}




		switch ($field) {
			case "id_societe":
				if ($affaire) {
					return $affaire['id_societe'];
				}
				break;
			case "id_refinanceur" :
					if($comite){	return $comite["id_refinanceur"];	}
				break;
			case "score":
				return ATF::$usr->trans(ATF::societe()->select($societe, "score"), "societe_score");
			case "avis_credit":
				return ATF::$usr->trans(ATF::societe()->select($societe, "avis_credit"), "societe_avis_credit");
			case "id_user":
				return ATF::$usr->get('id_user');
			case "date":
				return date("d-m-Y");
			case "id_contact":
				if ($affaire) {
					$devis = ATF::affaire()->getDevis($affaire['id_affaire']);
					return $devis->infos['id_contact'];
				}
			break;
			case "prix":
				if ($affaire) {
					$devis = ATF::affaire()->getDevis($affaire['id_affaire']);
					return $devis->infos['prix'];
				}
			break;
			case "description":
				if ($affaire) {
					return $affaire['affaire'];
				}
			break;
			case "loyer_actualise":
				if ($affaire) {
					return ATF::affaire()->getCompteTLoyerActualise($affaire);
				}
			break;
		}

		return parent::default_value($field,$s,$request);
	}

	/**
	* Impossible de supprimer une demande refi qui a été facturée
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_delete($id){
		$id = $this->decryptId($id);
		ATF::facture()->q->reset()->addCondition("id_demande_refi",$id)->setCount();
		$count=ATF::facture()->sa();
		if($count["count"]>0){
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il y a une ".ATF::$usr->trans("facture")." liée.",878);
		}elseif($this->select($id,"etat")=="valide"){
			throw new errorATF("Impossible de modifier/modifier cette ".ATF::$usr->trans($this->table)." car elle est ".ATF::$usr->trans($this->select($id,"etat")).".",877);
		}else{
			return true;
		}
	}

	/**
	* Impossible de modifier une demande refi qui a été facturée
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		return $this->can_delete($id);
	}

	/** Permet de récupérer le refinanceur
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int id_affaire : identifiant de l'affaire courante
	*/
	public function id_refinanceur($id_affaire) {
		$this->q->reset()->addField("id_refinanceur")->setStrict()
						->addCondition('id_affaire',$id_affaire)->addCondition('etat','valide')
						->setDimension("cell");
		return $this->select_all();
	}

};
?>