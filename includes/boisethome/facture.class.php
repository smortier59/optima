<?
require_once dirname(__FILE__)."/../facture.class.php";
class facture_boisethome extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = "facture";
		$this->colonnes['fields_column'] = array(
			'facture.ref'=>array("width"=>100,"align"=>"center")
			,'facture.id_societe'
			,'facture.id_devis_lot'
			,'facture.date'=>array("width"=>100,"align"=>"center")
			,'facture.date_previsionnelle'=>array("width"=>100,"align"=>"center")
			,'facture.etat'=>array("width"=>30,"renderer"=>"etat")
			,'facture.date_effective'=>array("width"=>100,"align"=>"center")
			,'facture.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'prix_ttc'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'retard'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"duree","width"=>80)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			,'actions'=>array("custom"=>true,"nosort"=>true,"align"=>"center","width"=>100,"renderer"=>"actionsFacture")
		);

		$this->colonnes['primary'] = array(
			"id_societe"
			,"id_affaire"
			,"id_devis_lot"
			,"type_facture"
			,'prix'
			,'tva'
			,"date"
			,'date_previsionnelle'=>array("obligatoire"=>true)
			,'date_relance'
			,'date_effective'
			,'etat'
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);

		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] = array('ref','id_user','etat','date_effective');
		$this->colonnes['bloquees']['update'] = array('ref','id_user','email','emailCopie','emailTexte');

		$this->fieldstructure();

		$this->onglets = array('facture_ligne');
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true,"quickMail"=>true);
		$this->field_nom = "ref";
		$this->formExt=true;
	}

	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		$this->q
			->addJointure("facture","id_societe","societe","id_societe")
			->addField("ROUND(facture.prix*facture.tva,2)","prix_ttc")
			->addField("TO_DAYS(IF(facture.date_effective IS NOT NULL,facture.date_effective,NOW())) - TO_DAYS(facture.date_previsionnelle)","retard")
			->addGroup("facture.id_facture");
		$return = parent::select_all($order_by,$asc,$page,$count);

		foreach ($return['data'] as $k=>$i) {
			if ($i['facture.id_facture']) { // Seulement si on a une clé, car dans lec as d'un autocomplete on demande pas ce field...
				if ($i['solde']>0) {
					$return['data'][$k]['allowSolde'] = true;
				} else {
					$return['data'][$k]['allowSolde'] = false;
				}
				if ($i['interet']>0) {
					$return['data'][$k]['interet'] = round($i['interet'],2);
					$return['data'][$k]['allowFactureInteret'] = true;
				} else {
					$return['data'][$k]['allowFactureInteret'] = false;
				}
			}
		}
		return $return;
	}

	/**
    * Retourne la TVA pour une facture
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return int
    */
	public function getTVA($id_societe=false){
		$societe=ATF::societe()->select($id_societe);
		$adrr_FR = (($societe["facturation_id_pays"]=="FR" && $societe["facturation_adresse"]) || (!$societe["facturation_adresse"] && $societe["id_pays"]=="FR"));
		$TVA_FR = substr($societe["reference_tva"],0,2)=="FR";
		if ($TVA_FR || !$societe["reference_tva"] && $adrr_FR) {
			return  __TVA__;
		} else {
			return 1;
		}
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @param string $field
	* @return string
    */
	public function default_value($field){
		if(ATF::_r('id_devis_lot')){
			$devis_lot=ATF::devis_lot()->select(ATF::_r('id_devis_lot'));
			$infos=ATF::devis()->select($devis_lot['id_devis']);
		}elseif(ATF::_r('id_affaire')){
			$infos=ATF::affaire()->select(ATF::_r('id_affaire'));
		}
		switch ($field) {
			case "id_societe":
			case "id_affaire":
				return $infos[$field];

			case "prix":
				return $infos[$field] * $devis_lot["payer_pourcentage"] / 100;
			case "tva":
				return $this->getTVA($infos["id_societe"]);
			case "date":
				return date("Y-m-d");
			case "date_previsionnelle":
				return date("Y-m-d",strtotime("+1 month"));
			case "date_relance":
				return date("Y-m-d",strtotime("+6 weeks"));
			default:
				return parent::default_value($field);
		}
	}

	/**
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
 		$preview=$infos["preview"];
		$type_check=$infos[$this->table]["mode"];
		$finale=$infos[$this->table]["finale"];

		$this->infoCollapse($infos);

		if(!$infos["id_societe"]){
			throw new errorATF("Vous devez spécifier la société (Entité)",167);
		}

		if(!$infos["id_affaire"]){
			throw new errorATF("Vous devez spécifier une affaire, sinon cochez la case CREER AFFAIRE SANS DEVIS, alors une affaire sera créée avec pour nomination 'Libellé affaire sans devis'.",160);
		}

		if($infos["emailTexte"]){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
		}else{
			$email=false;
		}

		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"],$infos["preview"]);

		$infos["id_user"] = ATF::$usr->getID();
		$infos["ref"] = ATF::affaire()->getRef($infos["date"],"facture");

		if(!$infos["date_previsionnelle"]){
			$infos["date_previsionnelle"] = date('Y-m-d',strtotime(date("Y-m-d")." + 30 day"));
		}

		$societe=ATF::societe()->select($infos["id_societe"]);

		//Seuls les associés peuvent modifier la tva
		$tva=$this->getTVA($societe["id_societe"]);
		if($tva!=$infos["tva"] && ATF::$usr->get("id_profil")!=1){
			$profil=ATF::profil()->select(1);
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("error_403_facture_tva"),array("profil"=>$profil["profil"], "tva"=>$tva))
				,ATF::$usr->trans("Droits_d_acces_requis_pour_cette_operation")
			);
			$infos["tva"] = $tva;
		}
		if($type_check=="avoir"){
			$infos["prix"]=0-$infos["prix"];
		}

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		// Affaire
		if($infos["id_affaire"]){
			$etat_affaire=ATF::affaire()->select($infos["id_affaire"],"etat");
			if ($etat_affaire=="devis") {
				$affaire["id_affaire"]=$infos["id_affaire"];
				$affaire["etat"]="facture";
				ATF::affaire()->u($affaire,$s);
			}
		}

		//Facture
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base	
			/* MAIL */
			if($email){
				if(!$email["email"]){
					$id_contact_facturation=ATF::societe()->select($infos["id_societe"],"id_contact_facturation");
					if($id_contact_facturation){
						if(!$recipient=ATF::contact()->select($id_contact_facturation,"email")){
							ATF::db($this->db)->rollback_transaction();
							throw new errorATF("Il n'y a pas d'email pour ce contact",166);
						}
					}else{
						ATF::db($this->db)->rollback_transaction();
						throw new errorATF("Il n'y a pas d'email pour ce contact",166);
					}
				}else{
					$recipient = $email["email"];
				}
				$facture = ATF::facture()->select($last_id);
				$from = ATF::user()->select(ATF::$usr->getID(),"email");

				$info_mail["objet"] = "Votre Facture référence : ".$facture["ref"];
				$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
				$info_mail["html"] = false;
				$info_mail["template"] = 'devis';
				$info_mail["texte"] = $email["texte"];
				$info_mail["recipient"] = $recipient;
				//Ajout du fichier
				$path = $this->filepath($last_id,"fichier_joint");

				$this->facture_mail = new mail($info_mail);
				$this->facture_mail->addFile($path,$facture["ref"].".pdf",true);
				$this->facture_mail->send();

				if($email["emailCopie"]){
					$info_mail["recipient"] = $email["emailCopie"];
					$this->facture_copy_mail = new mail($info_mail);
					$this->facture_copy_mail->addFile($path,$facture["ref"].".pdf",true);
					$this->facture_copy_mail->send();
				}
			}

			ATF::db($this->db)->commit_transaction();
		}

		ATF::affaire()->redirection("select",$infos["id_affaire"]);

		return $this->cryptId($last_id);
	}

};