<?
/**
* Classe Societé
* Cet objet permet de gérer les entités au sein du CRM
* @package Optima
*/
class societe extends classes_optima {
	/**
	* Constructeur par défaut
	*/
	public function __construct() {
		parent::__construct();
		//$this->controlled_by = "societe";
		$this->table = __CLASS__;

		/*-----------Quick Insert-----------------------
		$this->quick_insert = array('societe'=>'societe');*/

		/*-----------Colonnes Select all par défaut------------------------------------------------*/
		$this->colonnes['fields_column'] = array(
			'societe.societe'
			,'societe.tel' => array("tel"=>true)
			,'societe.fax' => array("tel"=>true)
			,'societe.email'
			,'societe.ville'
			,'dernierSuivi'=>array("custom"=>true,"nosort"=>true)
		);

		// Panel prinicpal
		$this->colonnes['primary'] = array(
			"ref"
			,"nom"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"societe"
				,"nom_commercial"
			))
			,"sirens"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"siren"
				,"siret"
			))
			,"id_famille"
			,"id_owner"
			,"etat"
			,"id_filiale"
			,"date_creation"
			,"relation"
			,"fournisseur"
		);

		// Adresse
		$this->colonnes['panel']['adresse_complete_fs'] = array(
			"id_contact_commercial"
			,"adresse"
			,"adresse_2"
			,"adresse_3"
			,"cp_ville"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"cp"
				,"ville"
			))
			,"id_pays"
		);
		$this->panels['adresse_complete_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);

		// Adresse de facturation
		$this->colonnes['panel']['adresse_facturation_complete_fs'] = array(
			"id_contact_facturation"
			,"facturation_adresse"
			,"facturation_adresse_2"
			,"facturation_adresse_3"
			,"facturation_cp_ville"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"facturation_cp"
				,"facturation_ville"
			))
			,"facturation_id_pays"
		);
		$this->panels['adresse_facturation_complete_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);

		// Coordonnées supplémentaires
		$this->colonnes['panel']['coordonnees_supplementaires_fs'] = array(
			"tel_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"tel"=>array("quick_update"=>true,"renderer"=>"tel","custom"=>true,"tel"=>true)
				,"fax"=>array("renderer"=>"tel")
			))
			,"email"=>array("quick_update"=>true)
			,"web"
			,"coordonnees_gps"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"latitude"
				,"longitude"
			))
		);
		$this->panels['coordonnees_supplementaires_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);

		// Informations supplémentaires de facturation
		$this->colonnes['panel']['facturation_fs'] = array(
			"banque"
			,"banque_societe"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"reference_tva"
				,"iban"
				,"rib"
			))
			,"bic"
			,"swift"
			,"id_devise"
			,"id_termes"
			,"solde_et_relance"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"delai_relance"
				,"solde"
			))
		);
		$this->panels['facturation_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);

		// Coordonnées supplémentaires (codes)
		$this->colonnes['panel']['codes_fs'] = array(
			"les_codes"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"code_groupe"
				,"code_fournisseur"
			))
			,"liens"
		);
		$this->panels['codes_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);

		// Panel prinicpal des coordonnées
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'adresse_complete_fs')
			,"adresse_facturation_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'adresse_facturation_complete_fs')
			,"coordonnees_supplementaires"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'coordonnees_supplementaires_fs')
			,"facturation"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'facturation_fs')
			,"codes"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'codes_fs')
		);
		$this->panels['coordonnees'] = array("visible"=>true);

		// Structure et secteur
		$this->colonnes['panel']['structure_secteur_fs'] = array(
			"structure_societe"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"structure"
				,"activite"
				  ,"naf"
			))
			,"information_financiere"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"capital"
				,"ca"
			))
			,"taille"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"effectif"
				,"nb_employe"
			))
			,"id_secteur_commercial"
			,"id_secteur_geographique"
		);
		$this->panels['structure_secteur_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);

		// Panel prinicpal des catactéristiques
		$this->colonnes['panel']['caracteristiques'] = array(
			"notes"
			,"structure_secteur"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'structure_secteur_fs')
			,"partenaire"
		);

		/*-----------Colonnes bloquées select -----------------------*/
		$this->colonnes['bloquees']['select'] = array(
			"id_contact_commercial"
			/* mis en commentaire pour le select_all extjs
			,"adresse_2"
			,"adresse_3"
			,"ville"
			,"cp"
			,"id_pays"
			,"facturation_adresse_2"
			,"facturation_adresse_3"
			,"facturation_ville"
			,"facturation_cp"
			,"facturation_id_pays"*/
		);

		/*-----------Colonnes bloquées insert -----------------------*/
		$this->colonnes['bloquees']['insert'] = array(
			"link_hotline"
			,"ref"
			,"date"
			,"id_contact_facturation"
			,"id_contact_commercial"
			,"link_hotline"=> array("custom"=>true)
		);

		/*-----------Colonnes bloquées update -----------------------*/
		$this->colonnes['bloquees']['update'] = array(
			"link_hotline"
			,"ref"
			,"credits"
			,"date"
			,"link_hotline"=> array("custom"=>true)
		);

		$this->colonnes['bloquees']['recherche'] = array("id_".$this->table);

		$this->fieldstructure();

		$this->onglets = array(
			'contact'=>array('opened'=>true)
			,'suivi'=>array('opened'=>true)
			,'affaire'=>array('opened'=>true)
			,'devis'
			,'commande'
			,'facture'
			,'tache'
			,'ged'
			,'societe'=>array('field'=>'societe.id_filiale')/*,'societe_domaine'*/
		);

		$this->autocomplete = array(
			"field"=>array("societe.societe","societe.nom_commercial")
			,"show"=>array("societe.societe")
			,"popup"=>array("societe.societe","societe.nom_commercial")

//			,"where"=>array("societe.societe"=>"nom","societe.nom_commercial"=>"detail")
			,"view"=>array("societe.societe","societe.nom_commercial","societe.tel")
		);

		/* Définition statique des clés étrangère de la table */
		$this->foreign_key["id_contact_facturation"] = "contact";
		$this->foreign_key["id_contact_commercial"] = "contact";
		$this->foreign_key["facturation_id_pays"] = "pays";
		$this->foreign_key["id_filiale"] = "societe";
		$this->foreign_key["id_owner"] = "user";

		//$this->shortcut = array('shortcut','help','release');

		$this->gmap = true;
		$this->quick_action['select_all'][] = "geolocalisation";
		$this->quick_action['select'][] = "geolocalisation";

		$this->no_update_all = false; // Pouvoir modifier massivement

		// @todo Trouver un autre moyen, genre avec la société de l'utilisateur courant par exemple
		$this->maSociete = $this->select(1);

		$this->files["facturation_rib"] =true;

		$this->addPrivilege("send_identifiants_hotline");
		$this->addPrivilege("autocompleteFournisseurs");
		$this->addPrivilege("branch");
		$this->addPrivilege("rpcGetForMobile");
		$this->addPrivilege("rpcGetContactsForMobile");
		$this->addPrivilege("rpcGetRecentToRecallForMobile");
		$this->addPrivilege("rpcSetRecalledForMobile","update");
		$this->addPrivilege("rpcGetProximityForMobile");
		$this->addPrivilege("sendMails");
		$this->addPrivilege("getInfosFromCREDITSAFE");
		$this->addPrivilege("export_societe_contact");

		// on montre que pour joindre la table domaine, on passe par une table de jointure qui est societe_domaine, si on créé un filtre dans le module société
		$this->listeJointure['domaine']="societe_domaine";
	}

	/**
	* Retourne la ref maximum du mois en cours
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $prefixe Le préfixe de la référence (exemple SLI0911)
	* @return int la référence max
	*/
	public function get_max_ref($prefixe){
		//Recherche du max en base
		$this->q->reset()
			->addField('MAX(SUBSTRING(ref FROM -4))','max')
			->addCondition('ref',$prefixe.'%','OR',false,'LIKE');
		$result=$this->sa();

		if(isset($result[0]['max'])){
			return intval($result[0]['max'])+1;
		}else{
			return 1;
		}
	}

	/**
	* Retourne le préfixe utilisé, peut être surchargé
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return string $prefix
	*/
	public function create_ref_prefix(){
		return "S";
	}

	/**
	* Construit la référence de l'entité (spécifique à chaque Optima)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $s La session
	* @return string $ref la référence de l'entité
	*/
	public function create_ref(&$s){
		$ref=$this->create_ref_prefix();
		if(ATF::$usr->get('id_agence')){
			//Recherche agence + date
			$ref.=strtoupper(
				substr(
					ATF::agence()->select(ATF::$usr->get('id_agence'),'agence'),0,2)
				).date('ym');

			//Recherche du maximum
			$max=$this->get_max_ref($ref);
			if($max<10){
				$ref.='000'.$max;
			}elseif($max<100){
				$ref.='00'.$max;
			}elseif($max<1000){
				$ref.='0'.$max;
			}elseif($max<10000){
				$ref.=$max;
			}else{
				throw new errorATF(ATF::$usr->trans('ref_too_high'),80853);
			}

			return $ref;
		}else{
			throw new errorATF(ATF::$usr->trans('societe_agence_user_false'),80846);
		}
	}

	/**
	* Surcharge de l'insertion pour les sociétés
	* Il y a la prise en charge de la création de référence ainsi que l'attachement au id_owner
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param array $infos Les informations à insérer
	* @param array $s la session
	* @param array $files Les fichiers uploadés éventuels
	* @param array $cadre_refreshed Le cadre refreshed utilisé pour le rafraichissement ajax
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);

		if ($this->desc && isset($this->desc["ref"])) {
			$infos['ref']=$this->create_ref($s);
		}

		//--Set de l'id Owner (créateur de l'entité)--
		if(!$infos['id_owner']) $infos['id_owner']=ATF::$usr->getID();

		//--Insertion en base de données--
		$retour=false;

		//Transactionel
		ATF::db($this->db)->begin_transaction();

		try{
			$retour=parent::insert($infos,$s,$files,$cadre_refreshed);
		}catch(errorATF $e){
			ATF::db($this->db)->rollback_transaction();
			throw $e;
		}

		//--Commit + retour--
		ATF::db($this->db)->commit_transaction();
		return $retour;
	}

	/**
	* Grise les contacts qui sont inactifs
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array donnees : les donnees de la ligne du select_all (donc qui se base sur les colonnes)
	* @return string class css à appliquer
	*/
	public function applique_css(&$donnees){
		if($etat=$donnees['societe.etat']){
			if($etat=="inactif")return 'grise';
		}else{
			$etat=$this->select($donnees['societe.id_societe'],'etat');
			if($etat=="inactif")return 'grise';
		}
		return NULL;
	}

	/**
	* Surcharge de la méthode update dans le cas où l'on rends la société inactive, on rends les contacts s'y rattachant inactifs également
	* ou dans le cas d'un changement des informations sur l'adresse, on réinitialise les coordonnées gps
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function update($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
        $infos['id_societe'] = $this->decryptId($infos['id_societe']);
		//on check l'état de la société avant l'update
		$infos_soc=$this->select($infos["id_".$this->table]);
		//si il y a un changement dans l'adresse, le cp ou la ville on reinitialise les coordonnées gps
		if((isset($infos['adresse']) && $infos_soc['adresse']!=$infos['adresse']) || (isset($infos['cp']) && $infos_soc['cp']!=$infos['cp']) || (isset($infos['ville']) && $infos_soc['ville']!=$infos['ville'])){
			$infos['latitude']=NULL;
			$infos['longitude']=NULL;
		}

		$retour=parent::update($infos,$s,$files,$cadre_refreshed);

		//--On met les contacts en inactifs dans le cas où il s'agit de cette modification
		if($infos_soc['etat']!=$infos['etat'] && $infos['etat']=='inactif'){
			ATF::contact()->q->reset()->addField("id_contact")->addCondition('id_societe',$infos['id_societe'])->addCondition('etat','actif');
			foreach(ATF::contact()->select_all() as $key=>$item){
				ATF::contact()->update(array("id_contact"=>$item['id_contact'],"etat"=>"inactif"));
			}
		}

		return $retour;
	}

	/**
	* Autocomplete retournant les fournisseurs
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteFournisseurs($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->where("fournisseur","oui");
		return parent::autocomplete($infos,false);
	}

	/****************************** Treeview des filiales ************************************/

	/** Permet de créer le tableau qui va afficher le treeview de la société
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return string json du tableau structuré
	*/
	public function branch($infos,&$s,$files=NULL,&$cadre_refreshed){
		if ($infos["node"]=="source") {
			//on récupère la société originelle et on flag toutes les sociétés par lesquelles on est passé pour les expand
			$return=$this->societeOriginelle($infos["valeur"],$infos["valeur"]);
		}else{
			$this->q->reset()->addOrder("societe","asc")
							->addCondition("id_filiale",$infos["node"]);
			if ($items = parent::select_all()) {
				foreach ($items as $item) {
					if($this->estEnfant($item['id_societe'],$infos["valeur"]))$item['expanded']=true;
					ATF::contact()->q->reset()->setCountOnly()->addCondition("id_societe",$item['id_societe']);
					$item['nbre_contact']=ATF::contact()->select_all();
					$return[]=$this->feuille($item);
				}
			}
		}

		$infos['display'] = true;

		return json_encode($return);
	}

	/** Permet de récupérer l'ensemble des sociétés d'un goupe (société parente, filles, soeurs...)
	* @author Mathieu mtribouillard <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return array $societes tableau de societe
	*/
	public function getGroup($id_societe){
		//Pour prendre les factures des sociétés parentes
		if($societeOriginelle=$this->societeOriginelle($id_societe,$id_societe)){
			$societes[]=$this->select($societeOriginelle[0]["id"]);
			$societes=$this->getFilliale($societeOriginelle[0]["id"],$societes);
			return $societes;
		}else{
			return false;
		}
	}

	/** Permet de récupérer l'ensemble des sociétés d'un goupe (société parente, filles, soeurs...)
	* @author Mathieu mtribouillard <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @param array $societes tableau de societe
	* @return array $societes tableau de societe
	*/
	public function getFilliale($id_societe,$societes){
		ATF::societe()->q->reset()->addCondition("id_filiale",$id_societe);
		if($societeFiliale=ATF::societe()->sa()){
			foreach($societeFiliale as $item){
				$societes[]=$item;
				$societes=$this->getFilliale($item["id_societe"],$societes);
			}
		}
		return $societes;
	}

	/** Permet de récupérer la société d'origine de la société de la fiche
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param integer id_societe : société de la fiche / société qui a pour filiale celle passée en paramètre
	* @param integer id_societe_courante : société de la fiche
	*/
	public function societeOriginelle($id_societe,$id_societe_courante){
		$soc_courante=$this->select($id_societe);
		//pour éviter le bouclage si les filiales n'ont toujours pas été corrigées
		if($soc_courante['id_filiale']!=$id_societe){
			if($soc_courante['id_filiale']){
				$soc_famille=$this->societeOriginelle($soc_courante['id_filiale'],$id_societe_courante);
			}else{
				if($id_societe!=$id_societe_courante)$soc_courante['expanded']=true;
				ATF::contact()->q->reset()->setCountOnly()->addCondition("id_societe",$soc_courante['id_societe']);
				$soc_courante['nbre_contact']=ATF::contact()->select_all();
				$soc_famille[]=$this->feuille($soc_courante);
			}
		}
		return $soc_famille;
	}

	/** Permet de déterminer si la société passé en paramètre est une société parente à la société de la fiche
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param integer id_societe : société à tester
	* @param integer id_societe_enfant : société de la fiche
	* @return boolean : vrai si il s'agit d'une société parente, non dans le cas contraire
	*/
	public function estEnfant($id_societe,$id_societe_enfant){
		$return = false;
		//permet de voir si la société est une société parente de la société enfant envoyée
		foreach($this->select_special('id_filiale',$id_societe) as $key=>$item){
			//retourne vrai, si la société enfant est la société de la fiche sur laquelle on est
			if($item['id_societe']==$id_societe_enfant){
				return true;
			//retourne vrai, si la societe enfant est une societe parente de la societe de la fiche sur laquelle on est
			}elseif($this->estEnfant($item['id_societe'],$id_societe_enfant)){
				return true;
			}
		}
		return $return;
	}

	/** Permet de structurer les informations nécessaires à l'affichage du treeview
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @parem array infos : informations concernant la société
	* @return array : le tableau nécessaire à l'affichage du treeview
	*/
	public function feuille($infos){
		//on regarde si la société a des enfants, si oui, on fait en sorte que l'icône qui lui appartient soit un dossier
		//sinon on affiche un fichier
		$this->q->reset()->setCountOnly()->addCondition("id_filiale",$infos["id_societe"]);
		$count=parent::select_all();
		$id_soc=$this->cryptId($infos["id_societe"]);
		return array(
			"text"=>$infos["societe"].($infos["nbre_contact"]?" (".$infos["nbre_contact"]." ".ATF::$usr->trans("contact","societe").")":"")
			,"id"=>$infos["id_societe"]
			,"leaf"=>$count==0
			,"href"=>"societe-select-$id_soc.html"
			,"cls"=>$count==0?"file":"folder"
			,"expanded"=>$infos['expanded']
		);
	}
	/****************************** Fin Treeview des filiales ************************************/

	/**
	* Méthode ajax pour appeler les société depuis un mobile
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetForMobile(){
		ATF::$cr->block("top");
		$this->q->reset()->addField("societe")->addField("id_societe")->addOrder("societe");
		$data=$this->select_all();
		foreach ($data as $k => $i) {
			$societe = substr(ucfirst(util::removeAccents(trim($i["societe"]))),0,1);
//			if (!$societe) {
//				$societe="-";
//			}
			$data[$k]["indexAlpha"]=$societe;
		}
		return util::cleanForMobile($data);
	}

	/**
	* Retourne les coordonnées d'une société et de ses contacts
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetContactsForMobile($infos){
		ATF::$cr->block("top");

		// Adresse de la société
		$s = $this->select($infos["id"]);
		$return[] = array(
			"detail" => ATF::$usr->trans("adresse","societe")
			,"text" => $s["adresse"]." ".$s["adresse_2"]." ".$s["adresse_3"]." ".$s["cp"]." ".$s["ville"]." ".ATF::pays()->nom($s["id_pays"])
			,"group" => "1societe"
			,"groupTitle" => $s["societe"]
			,"type" => "address"
		);

		// Numéro de téléphone et de fax de la société
		foreach (array("tel","fax") as $f) {
			if ($s[$f]) {
				$return[] = array(
					"detail" => ATF::$usr->trans($f,"societe")
					,"text" => str_replace(" ","",$s[$f])
					,"group" => "1societe"
					,"groupTitle" => $s["societe"]
					,"type" => "phone"
					,"id_societe" => $s["id_societe"]
				);
			}
		}

		// Numéro de téléphone des contacts
		ATF::contact()->q->reset()
			->addField("id_contact")
			->addField("adresse")
			->addField("adresse_2")
			->addField("adresse_3")
			->addField("cp")
			->addField("ville")
			->addField("id_pays")
			->addField("tel")
			->addField("fax")
			->addField("gsm")
			->addField("fonction")
			->addField("etat")
			->where("id_societe",$infos["id"]);
		if ($data=ATF::contact()->select_all()) {
			foreach ($data as $k => $i) {
				$baseArray = array(
					"group" => "2".ATF::contact()->nom($i["id_contact"])
					,"groupTitle" => ATF::contact()->nom($i["id_contact"]).($i["fonction"] ? " (".$i["fonction"].")" : NULL)
					,"etat" => $i["etat"]
				);
				if ($i["adresse"]) {
					$return[] = array_merge($baseArray,array(
						"detail" => ATF::$usr->trans("adresse","contact")
						,"text" => $i["adresse"]." ".$i["adresse_2"]." ".$i["adresse_3"]." ".$i["cp"]." ".$i["ville"]." ".ATF::pays()->nom($i["id_pays"])
						,"type" => "address"
					));
				}
				foreach (array("tel","fax","gsm") as $f) {
					if ($i[$f]) {
						$return[] = array_merge($baseArray,array(
							"detail" => ATF::$usr->trans($f,"contact")
							,"text" => str_replace(" ","",$i[$f])
							,"type" => "phone"
							,"id_contact" => $i["id_contact"]
						));
					}
				}
			}
		}

		// Ajout des 3 derniers suivis
		ATF::suivi()->q->reset()
			->addField("suivi.date")
			->addField("suivi.texte")
			->addField("suivi.id_suivi","id")
			->where("suivi.id_societe",$infos["id"])
			->addOrder("suivi.date","desc")
			->setLimit(3);
		if ($data=ATF::suivi()->select_all()) {
			foreach ($data as $k => $i) {
				$return[] = array(
					"group" => "0suivis"
					,"groupTitle" => ATF::$usr->trans("suivi","module")
					,"detail" => ATF::$usr->date_trans($i["suivi.date"])." ".$i["suivi.intervenant_client"]." ".$i["suivi.intervenant_societe"]
					,"text" => $i["suivi.texte"]
					,"id_suivi" => $i["suivi.id_suivi_fk"]
					,"type" => "suivi"
					,"intervenant_societe" => $i["suivi.intervenant_societe"]
					,"contact" => $i["suivi.intervenant_client"]
					,"notifie" => $i["suivi.notifie"]
					,"texte" => $i["suivi.texte"]
					,"societe" => $s["societe"]
					//,"etat" => ""
				);
			}
		}
		return util::cleanForMobile($return);
	}

	/**
	* Retourne les sociétés aléatoirement
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetRecentToRecallForMobile($infos,$noreset=false){
		ATF::$cr->block("top");
		if ($noreset!==true) {
			$this->q->reset();
		}
		$this->q
			->addField('id_societe')->addField('societe')
			->addOrder("recallCounter","asc")->addOrder("RAND()")
			->where("fournisseur","non")->where("partenaire","non")->where("etat","actif")
			->setLimit(2);

		// Prospects
		$this->q->where("relation","prospect","OR","rel");
		$data = $this->select_all();
		foreach ($data as $k => $i) {
			$return[] = array(
				"text" => $i["societe"]
				,"id_societe" => $i["id_societe"]
				,"group" => "prospect"
				,"groupTitle" => ATF::$usr->trans("prospect","societe_relation")
			);
		}

		// Client
		$this->q->where("relation","client","OR","rel","=",true);
		$data = $this->select_all();
		foreach ($data as $k => $i) {
			$return[] = array(
				"text" => $i["societe"]
				,"id_societe" => $i["id_societe"]
				,"group" => "client"
				,"groupTitle" => ATF::$usr->trans("client","societe_relation")
			);
		}
		return util::cleanForMobile($return);
	}

	/**
	* Détermine qu'on a rappelé ce client avec le mobile
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcSetRecalledForMobile($infos){
		ATF::$cr->block("top");

		if (!is_numeric($infos["id_societe"])) {
			$infos["id_societe"] = ATF::contact()->select($infos["id_contact"],"id_societe");
		}

		$this->increase($infos["id_societe"],"recallCounter");

		// Ajout du suivi
		$suivi = array(
			"id_user"=>ATF::$usr->getID()
			,"id_societe"=>$infos["id_societe"]
			,"texte"=>$infos["texte"]." (Optima mobile)"
			,'suivi_societe'=>array(ATF::$usr->getID())
			//,'public'=>'oui'
			//,'suivi_notifie'=>array(0=>ATF::societe()->select($cmd['id_societe'],'id_owner'))
		);
		if (is_numeric($infos["id_contact"])) {
			$suivi["suivi_contact"]=array($infos["id_contact"]);
		}
		ATF::suivi()->insert($suivi);

		return true;
	}

	/**
	* Retourne les société à proximité du point central des coordonnées de la fenêtre vue sur la map passés en paramètre
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetProximityForMobile($infos){
		ATF::$cr->block("top");
		$infos["nw"] = explode(",",$infos["nw"]);
		$infos["se"] = explode(",",$infos["se"]);
		$x = ($infos["nw"][0]+$infos["se"][0])/2;
		$y = ($infos["nw"][1]+$infos["se"][1])/2;
		$data = $this->getProximiteFromXY($x,$y);
		return (array)(util::cleanForMobile($data));
	}

	/**
	* Méthode spéciale par défaut "saCustom"
	* Appel la méthode de classe particulière à utiliser,  si $method =flase on utilise select_all
	* Utilisation dans generic_select_all
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $s : la session
	* @param string $method : methode de classe particulière à utiliser
	* @return array Résultat de la requête
	*/
	public function select_data(&$s,$method=false){
		if (!$method) {
			$method="saCustom";
		}
		return parent::select_data($s,$method);
	}

	/**
	* Surcharge du select-All
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function saCustom(){
		$this->q->addField("societe.etat");
		return $this->select_all();
	}

	/**
	* Module d'envoi de mails depuis société
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function sendMails($infos){

		$soc = explode("_",$infos["societe"]);
		if($infos["previsualiser"] === "true"){

			$mail=new mail(array(
							'recipient'=> ATF::user()->select(ATF::$usr->getID() , "email"),
							'objet'=>'[Prévisualisation] '.$infos["sujet"],
							"template"=>"societe_emails",
							"donnee"=>$infos["message"]
						 ));
			$mail->send();

		}else{
			foreach ($soc as $key => $value) {
				ATF::contact()->q->reset()->where("contact.id_societe", $this->decryptId($value));
				$contact = ATF::contact()->select_all();
				$contacte = "";
				foreach ($contact as $k => $v) {
					if($v["email"]){
						if($contacte == ""){
							$contacte = ATF::contact()->cryptId($v["id_contact"]);
						}else{
							$contacte = $contacte.",".ATF::contact()->cryptId($v["id_contact"]);
						}

						$mail=new mail(array(
							'recipient'=> $v["email"],
							'objet'=> $infos["sujet"],
							"template"=>"societe_emails",
							'donnee' => $infos["message"]
						 ));
						$mail->send();
					}
				}
				//Création du Suivi
				$suivi = array("suivi" => array("id_societe" => $this->decryptId($value),
												 "type" => "email",
												 "texte" => $infos["message"],
												 "date" => date("d-m-Y H:i"),
												 "suivi_contact" => $contacte,
												 "suivi_societe" => ATF::user()->decryptId(ATF::$usr->getID()),
												 "suivi_notifie" => NULL,
												 "filestoattach" => array( "fichier_joint" => NULL)

								));
				ATF::suivi()->insert($suivi);

			}

			$mail=new mail(array(
							'recipient'=> "interne@absystech.fr",
							'objet'=> $infos["sujet"],
							"template"=>"societe_emails",
							'donnee' => $infos["message"]
						 ));
			$mail->send();

		}


	}

	/** Renvoi l'adresse formatté pour une société
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getAddress($id,$inline=false) {
		$s = $this->select($id);
		$r = $s['adresse'];
		if ($s['adresse_2']) {
			$r .= " \n ".$s['adresse_2'];
		}

		if ($s['adresse_3']) {
			$r .= " \n ".$s['adresse_3'];
		}
		$r .= " \n ".$s['cp']." ".$s['ville'];

		if ($inline) $r = str_replace("\n","-",$r);

		return $r;
	}


	/** Interroge Cradit Safe pour récupérer les informations des sociétés.
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getInfosFromCREDITSAFE($infos) {
		if(__DEV__ === true){
			$response = file_get_contents("/home/optima/core/log/creditsafe.xml");
			$xml = simplexml_load_string($response);
		} else {
			 $xmlReq = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
		    <xmlrequest>
		        <header>
		            <username>".__CREDIT_SAFE_LOGIN__."</username>
		            <password>".__CREDIT_SAFE_PWD__."</password>
		            <operation>getcompanyinformation</operation>
		            <language>FR</language>
		            <country>FR</country>
		            <chargereference>REFGetCompanyInformation</chargereference>
		        </header>
		        <body>
		            <package>standard</package>
		            <companynumber>".str_replace(" ","",$infos['siret'])."</companynumber>

		        </body>
		    </xmlrequest>";
		    $url = 'https://www.creditsafe.fr/getdata/service/CSFRServices.asmx/GetData';
		    $params = array('requestXmlStr' => $xmlReq);


		    $response = $this->processCSRequest($url, $params);

			file_put_contents("/home/optima/core/log/creditsafe.xml",$response);

			$xml = simplexml_load_string($response);
		}


		if($xml->xmlresponse->body->errors){
			ATF::$msg->addWarning("Une erreur s'est produite pendant l'import crédit safe code erreur : ".(string)$xml->xmlresponse->body->errors->errordetail->code ,ATF::$usr->trans("notice_title"));
		}else{
			if($infos["returnxml"]){
				$data = $response;
			}else{
				$data = $this->cleanCSResponse($response);
			}
		}
		log::logger($data,'creditsafe');
		return $data;
	}


	/** Prépare les résultats de GGS creditsafe pour intégration dans Optima
	* @author Cyril CHARLIER  <ccharlier@absystech.fr>
	*/
	public function cleanGGSResponse($r) {
		$xml = $r;

		$item = $xml->RetrieveCompanyOnlineReportResult->Reports->Report;
		$company = $item->CompanyIdentification;

		$bi = $xml->xmlresponse->body->company->baseinformation;
		$b =  $xml->xmlresponse->body->company->balancesynthesis;

		// Nom de société
		$return['societe'] = (string)$company->BasicInformation->BusinessName;
		// Pays de société
		$return['id_pays'] = $company->BasicInformation->Country;

		// Adresse de société
		$return['adresse'] = (string)$company->BasicInformation->ContactAddress->Street.' '.(string)$company->BasicInformation->ContactAddress->HouseNumber;

		// CP de société
		$return['cp'] = (string)$company->BasicInformation->ContactAddress->PostalCode;

		// VILLE de société
		$return['ville'] = (string)$company->BasicInformation->ContactAddress->City;

		// telephone de société
		$return['tel'] = str_replace("/","",(string)$company->BasicInformation->ContactTelephoneNumber);

		// NAF de société
		$return['naf'] = (string)$company->BasicInformation->PrincipalActivity->ActivityCode;

		// Activite de société
		$return['activite'] = (string)$company->BasicInformation->PrincipalActivity->ActivityDescription;

		// Activite de société
		$return['structure'] = (string)$company->BasicInformation->LegalForm->_;

		// Activite de société
		$return['capital'] = (int)$item->ShareCapitalStructure->IssuedShareCapital;

		// Activite de société
		$dateparsee = explode('T',$company->BasicInformation->DateofCompanyRegistration);
		$return['date_creation'] = date("Y-m-d",strtotime($dateparsee[0]));

		// TVA
		$return['reference_tva'] = (string)$company->BasicInformation->VatRegistrationNumber;

		// NB employé de société
		//$return['nb_employe'] = (string)$bi->companyworkforce;
		// NOTE
		$return['cs_score'] = (string)$item->CreditScore->CurrentCreditRating->ProviderValue->_;

		// LIMITE
		$return['cs_avis_credit'] = (string)$item->CreditScore->CurrentCreditRating->CreditLimit->_;

		//Date information creditSafe
		$dateparsee = explode("-",explode('T',$item->CreditScore->DateOfLatestRatingChange)[0]);
		$return['lastaccountdate'] = (string) $dateparsee[2]."/".$dateparsee[1]."/".$dateparsee[0];
		// Créances
		$return['receivables'] = number_format(intval((string)$item->FinancialStatements->FinancialStatement['0']->BalanceSheet->TotalReceivables->_), 0, ",", "");

		// Placements + disponibilités
		$return['securitieandcash'] = number_format(intval((string)$item->FinancialStatements->FinancialStatement['0']->BalanceSheet->Cash->_) , 0, ",", "");

		// Produits d'exploitation
		$return['operatingincome'] =  number_format(intval((string)$item->FinancialStatements->FinancialStatement['0']->ProfitAndLoss->FinancialIncome->_) , 0, ",", "");

		// Chiffre d'affaires net
		$return['netturnover'] =  number_format(intval((string)$item->FinancialStatements->FinancialStatement[0]->ProfitAndLoss->ProfitAfterTax->_) , 0, ",", "");

		// Résultat d'exploitation
		$return['operationgprofitless'] = number_format(intval((string)$item->FinancialStatements->FinancialStatement[0]->ProfitAndLoss->OperatingProfit->_) , 0, ",", "");

		// Produits financiers
		$return['financialincome'] = number_format(intval((string)$item->FinancialStatements->FinancialStatement->ProfitAndLoss->FinancialIncome->_) , 0, ",", "");

		// Charges financières
		$return['financialcharges'] = number_format(intval((string)$item->FinancialStatements->FinancialStatement->ProfitAndLoss->FinancialExpenses) , 0, ",", "");


		$return["resultat_exploitation"] = number_format(intval((string)$b->balancesheet->profitloss->operatingprofitloss) , 0, ",", "");
		$return["capital_social"] = number_format(intval((string)$bi->sharecapital) , 0, ",", "");
		$return["capitaux_propres"] = number_format(intval((string)$b->balancesheet->passiveaccount->shareholdersequity) , 0, ",", "");
		$return["dettes_financieres"] = number_format(intval((string)$b->balancesheet->passiveaccount->financialliabilities) , 0, ",", "");

		// ETAT
		switch ((string)$company->BasicInformation->CompanyStatus->Code) {
			case '':
				$return['etat'] = "supprime";
			break;
			case 'Inactive':
				$return['etat'] = "inactif";
			break;
			case 'Active':
				$return['etat'] = "actif";
			break;
		}

		// CA
		$return['ca'] = (string)$item->CompanySummary->LatestTurnoverFigure->_;
		return $return;
	}
	/** Prépare les ésultats de creditsafe pour intégration dans Optima
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	private function cleanCSResponse($r) {


		$xml = simplexml_load_string($r);

		$bi = $xml->xmlresponse->body->company->baseinformation;
		$s = $xml->xmlresponse->body->company->summary;
		$b =  $xml->xmlresponse->body->company->balancesynthesis;


		$directors = $xml->xmlresponse->body->company->directors;

		$gerant = array();
		foreach ($directors->director as $key => $value) {
			if($value->typeofmanager == "Personne physique" && !preg_match("/Commissaire aux comptes/" ,$value->managerposition)){
				$return['gerant'][] = array("nom"=>(string)$value->familyname,
								  "prenom"=>(string)$value->christianname,
								  "fonction"=>(string)$value->managerposition);
			}
		}


		if ($bi->branches->numberofbranches>1) {
			for ($i=0; $i<=$bi->branches->numberofbranches; $i++) {
				if ((string)$bi->branches->branch[$i]->companynumber==(string)$bi->companynumber) {
					$goodBranch = $bi->branches->branch[$i];
				}
			}
		}else{
			$goodBranch = $bi->branches->branch;
		}

		$balancesheet = $xml->xmlresponse->body->company->balancesynthesis->balancesheet;

		// Nom de société
		$return['societe'] = (string)$bi->companyname;

		// SIRET de société
		$return['siret'] = (string)$bi->companynumber;

		// SIREN de société
		$return['siren'] = substr($return['siret'],0,9);

		// Pays de société
		$pays = ATF::pays()->ss("pays",(string)$bi->nationality);
		$return['id_pays'] = $pays[0]['id_pays'];

		// Adresse de société
		$return['adresse'] = (string)$s->postaladdress->address;

		// Adresse 2 de société
		$return['adresse_2'] = (string)$s->postaladress->additiontoaddress;

		// CP de société
		$return['cp'] = (string)$s->postcode;

		// VILLE de société
		$return['ville'] = (string)$s->municipality;

		// telephone de société
		$return['tel'] = (string)$bi->telephone;

		// FAX de société
		$return['fax'] = (string)$bi->fax;

		// NAF de société
		$return['naf'] = (string)$bi->activitycode;

		// Activite de société
		$return['activite'] = (string)$bi->activitydescription;

		// Activite de société
		$return['structure'] = (string)$bi->legalform;

		// Activite de société
		$return['capital'] = (int)$bi->sharecapital;

		// Activite de société
		$d = "01-".str_replace("/","-",(string)$bi->formationdate);
		$return['date_creation'] = date("Y-m-d",strtotime($d));

		// Activite de société
		$return['reference_tva'] = (string)$bi->vatnumber;

		// NB employé de société
		$return['nb_employe'] = (string)$bi->companyworkforce;

		// NOTE
		$return['cs_score'] = (string)$s->rating2013;

		// LIMITE
		$return['cs_avis_credit'] = (string)$s->creditlimit2013;

		//Date information creditSafe
		$return['lastaccountdate'] = (string)$bi->lastaccountdate;

		// Créances
		$return['receivables'] = number_format(intval((string)$balancesheet->activeaccount->receivables), 0, ",", " ");

		// Placements + disponibilités
		$return['securitieandcash'] = number_format(intval((string)$balancesheet->activeaccount->securitiesandcash) , 0, ",", " ");

		// Produits d'exploitation
		$return['operatingincome'] =  number_format(intval((string)$balancesheet->profitloss->operatingincome) , 0, ",", " ");

		// Chiffre d'affaires net
		$return['netturnover'] =  number_format(intval((string)$balancesheet->profitloss->netturnover) , 0, ",", " ");

		// Résultat d'exploitation
		$return['operationgprofitless'] = number_format(intval((string)$balancesheet->profitloss->operatingprofitloss) , 0, ",", " ");

		// Produits financiers
		$return['financialincome'] = number_format(intval((string)$balancesheet->profitloss->financialincome) , 0, ",", " ");

		// Charges financières
		$return['financialcharges'] = number_format(intval((string)$balancesheet->profitloss->financialcharges) , 0, ",", " ");

		$return["resultat_exploitation"] = number_format(intval((string)$b->balancesheet->profitloss->operatingprofitloss) , 0, ",", "");
		$return["capital_social"] = number_format(intval((string)$bi->sharecapital) , 0, ",", "");
		$return["capitaux_propres"] = number_format(intval((string)$b->balancesheet->passiveaccount->shareholdersequity) , 0, ",", "");
		$return["dettes_financieres"] = number_format(intval((string)$b->balancesheet->passiveaccount->financialliabilities) , 0, ",", "");

		// ETAT
		switch ((string)$x->status) {
			case 'Supprimé':
				$return['etat'] = "supprime";
			break;
			case 'Non diffusable':
				$return['etat'] = "non_diffusable";
			break;
			case 'Inactif':
				$return['etat'] = "inactif";
			break;
			case 'Actif économiquement':
				$return['etat'] = "actif";
			break;
			case 'Fermé':
				$return['etat'] = "ferme";
			break;
			case 'Transféré':
				$return['etat'] = "transfere";
			break;
			case 'Cessé économiquement (INSEE)':
				$return['etat'] = "cesse";
			break;
			case 'Liquidé':
				$return['etat'] = "liquide";
			break;
			case 'dormante':
				$return['etat'] = "veille";
			break;
		}

		// CA
		$return['ca'] = (string)$s->financialsummary->tradingtodate[0]->turnover;



		return $return;
	}

	/** Exécute la requête CURL a credit safe
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
    private function processCSRequest($url, $params) {
        if(!is_array($params)) return false;
        $post_params = "";
        foreach($params as $key => $val) {
            $post_params .= $post_params?"&":"";
            $post_params .= $key."=".$val;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 'true', for developer testing purpose curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);

        $data = curl_exec($ch);

        curl_close($ch);
        return html_entity_decode($data);
    }



    /** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_societe_contact($infos,$testUnitaire="false", $reset="true"){

	 	if($testUnitaire == "true"){
	 		$donnees = $infos;
		}else{
			if($reset == "true") $this->q->reset();
			$this->setQuerier(ATF::_s("pager")->create($infos['onglet']));
			$this->q->setLimit(-1)->unsetCount();
			$donnees = $this->select_all();
		}

        $onglet = str_replace("gsa_commande_", "", $infos["onglet"]);
		$onglet = str_replace("commande_", "", $onglet);
		if(is_numeric($onglet)){ $onglet = ATF::filtre_optima()->select($onglet , "filtre_optima"); }


		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle($onglet);
		$sheets=array("auto"=>$worksheet_auto);
		//$this->initStyle();
		if($donnees){
			$this->ajoutTitreExport($sheets,$donnees);
			$this->ajoutDonneesExport($sheets,$donnees);
		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_societe_contact.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
    }


    /** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function ajoutTitreExport(&$sheets, $donnees){
        $row_data = array();
     	//A =65 Z=90
     	$lettre1 = 65;
		$lettre2 = 0;

		$donnees[0]["contact.id_contact"] =  NULL;
		$donnees[0]["contact.civilite"] =   NULL;
		$donnees[0]["contact.nom"] =  NULL;
		$donnees[0]["contact.prenom"] =  NULL;
		$donnees[0]["contact.etat"] =  NULL;
		$donnees[0]["contact.tel"] =  NULL;
		$donnees[0]["contact.gsm"] =  NULL;
		$donnees[0]["contact.fax"] =  NULL;
		$donnees[0]["contact.email"] =  NULL;
		$donnees[0]["contact.fonction"] =  NULL;


     	foreach ($donnees[0] as $key => $value) {
     		$data = array();
     		$data = explode(".", $key);
     		if( !strpos($data[1],"_fk") ){

     			if($lettre1 <= 90 && $lettre2 == 0){
     				$char = chr($lettre1);
     				$lettre1++;
     			}else{
     				if($lettre2 == 0){
     					$lettre1 =  $lettre2 = 65;
     					$char = chr($lettre1).chr($lettre2);
     				}elseif($lettre2 == 90){
						$lettre1++;
						$lettre2 = 65;
						$char = chr($lettre1).chr($lettre2);
					}else{
						$lettre2++;
						$char = chr($lettre1).chr($lettre2);
					}
     			}
			    $row_data[$char] = array(ATF::$usr->trans($data[1],$data[0]),20);
     		}
     	}

        foreach($sheets as $nom=>$onglet){
             foreach($row_data as $col=>$titre){
				  $sheets[$nom]->write($col.'1',$titre[0]);
				  $sheets[$nom]->sheet->getColumnDimension($col)->setWidth($titre[1]);
            }
        }
    }



	/** Mise en place du contenu
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @param array $sheets : contient les 30 onglets
    * @param array $infos : contient tous les enregistrements
    */
    public function ajoutDonneesExport(&$sheets,$infos){
        $row_auto=1;

        foreach ($infos as $key => $value) {
        	$contacts = array();
        	ATF::contact()->q->reset()->addAllFields("contact")->where("contact.id_societe", $value["societe.id_societe"])->where("contact.etat","actif");
        	$contacts = ATF::contact()->select_all();

        	$i = 0;
        	foreach ($contacts as $k => $v) {
        		if($i > 0){
        			$infos[$key]["contact.id_contact"] .=  "\n".$v["contact.id_contact"];
					$infos[$key]["contact.civilite"]   .=  "\n".$v["contact.civilite"];
					$infos[$key]["contact.nom"] 	   .=  "\n".$v["ontact.nom"];
					$infos[$key]["contact.prenom"] 	   .=  "\n".$v["contact.prenom"];
					$infos[$key]["contact.etat"] 	   .=  "\n".$v["contact.etat"];
					$infos[$key]["contact.tel"] 	   .=  "\n".$v["contact.tel"];
					$infos[$key]["contact.gsm"]        .=  "\n".$v["contact.gsm"];
					$infos[$key]["contact.fax"] 	   .=  "\n".$v["contact.fax"];
					$infos[$key]["contact.email"] 	   .=  "\n".$v["contact.email"];
					$infos[$key]["contact.fonction"]   .=  "\n".$v["contact.fonction"];
        		}else{
        			$infos[$key]["contact.id_contact"] =  $v["contact.id_contact"];
					$infos[$key]["contact.civilite"] =   $v["contact.civilite"];
					$infos[$key]["contact.nom"] =  $v["contact.nom"];
					$infos[$key]["contact.prenom"] =  $v["contact.prenom"];
					$infos[$key]["contact.etat"] =  $v["contact.etat"];
					$infos[$key]["contact.tel"] =  $v["contact.tel"];
					$infos[$key]["contact.gsm"] =  $v["contact.gsm"];
					$infos[$key]["contact.fax"] =  $v["contact.fax"];
					$infos[$key]["contact.email"] =  $v["contact.email"];
					$infos[$key]["contact.fonction"] =  $v["contact.fonction"];
        		}
        		$i = $i+1;
        	}

        }


		foreach ($infos as $key => $value) {
			$row_data = array();
	     	//A =65 Z=90
	     	$lettre1 = 65;
			$lettre2 = 0;

			foreach ($value as $k => $v) {
	     		$data = array();
	     		$data = explode(".", $k);
	     		if( !strpos($data[1],"_fk") ){
	     			if($lettre1 <= 90 && $lettre2 == 0){
	     				$char = chr($lettre1);
	     				$lettre1++;
	     			}else{
	     				if($lettre2 == 0){
	     					$lettre1 =  $lettre2 = 65;
	     					$char = chr($lettre1).chr($lettre2);
	     				}elseif($lettre2 == 90){
							$lettre1++;
							$lettre2 = 65;
							$char = chr($lettre1).chr($lettre2);
						}else{
							$lettre2++;
							$char = chr($lettre1).chr($lettre2);
						}
	     			}
				    $row_data[$char] = array($v);
	     		}
	     	}

	     	if($row_data){
				$row_auto++;
				foreach($row_data as $col=>$valeur){
					$sheets['auto']->write($col.$row_auto, " ".$valeur[0]);
				}
			}
		}
	}

	/**
	* Permet de récupérer la liste des tickets hotline pour telescope
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	//$order_by=false,$asc='desc',$page=false,$count=false,$noapplyfilter=false
	public function _GET($get,$post) {

		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "id_societe";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit'] && !$get['no-limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;
		if ($get['no-limit']) $get['page'] = false;

		$this->q->reset();

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get["search"]);
		}

		if ($get['id']) {
			$this->q->where("societe.id_societe",$get['id'])->setLimit(1);
		} else {

			if ($get['filters']['active'] == "on") {
				$this->q->where("societe.etat","actif","OR","sta");
			}
			if ($get['filters']['inactive'] == "on") {
				$this->q->where("societe.etat","inactif","OR","sta");
			}
			if ($get['filters']['douteux'] == "on") {
				$this->q->where("societe.etat","douteux","OR","sta");
			}

			if (!$get['no-limit']) $this->q->setLimit($get['limit']);
		}

		switch ($get['tri']) {
			default:
				$get['tri'] = "societe.".$get['tri'];
			break;
		}

		$this->q->addAllFields("societe");
		$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}
			}
			// On récupère le domaine
			$data['data'][$k]['domaines'] = self::getDomaine($data['data'][$k]['id_societe_fk']);
		}
		if ($get['id']) {
      $return = $data['data'][0];
			$data['panels'] = $this->panels;
		} else {
			// Envoi des headers
			// Envoi des headers
			header("ts-total-row: ".$data['count']);
			if ($get['limit']) header("ts-max-page: ".ceil($data['count']/$get['limit']));
			if ($get['page']) header("ts-active-page: ".$get['page']);
			if ($get['no-limit']) header("ts-no-limit: 1");
      $return = $data['data'];
		}

		return $return;
	}

	/**
	 * Supprime une société
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param  array $get  Contient l'ID de la société
	 * @return Boolean TRUE si OK, FALSE si NOK
	 */
	public function _DELETE($get) {
		if (!$get['id']) throw new Exception("MISSING_ID",1000);
		$return['result'] = $this->delete($get);
    	// Récupération des notices créés
    	$return['notices'] = ATF::$msg->getNotices();
        return $return;
	}

	/**
	 * Renvoi les domaines des sociétés avec un flag pour indiquer l'affectation et une classe CLS pour le rendu
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param  INT $id id_societe
	 * @return array L'ensemble des domaines de sociétés avec le flag et la classe CLS
	 */
	public function getDomaine($id) {
		// Récupération des domaines
		ATF::domaine()->q->reset()
			->from("domaine","id_domaine","societe_domaine","id_domaine",false,false,false,"test")
			->where('societe_domaine.id_societe',$id,false,"test");
		if ($domaines = ATF::domaine()->sa()) {
			foreach ($domaines as $k=>$lines) {
				foreach ($lines as $k_=>$val) {
					if (strpos($k_,".")) {
						$tmp = explode(".",$k_);
						$domaines[$k][$tmp[1]] = $val;
						unset($domaines[$k][$k_]);
					}
				}
				switch ($lines['id_domaine']) {
					case 1: // Support
						$domaines[$k]['cls'] = 'warning';
					break;
					case 2: // Hardware
						$domaines[$k]['cls'] = 'success';
					break;
					case 3: // Web agency
						$domaines[$k]['cls'] = 'danger';
					break;
					case 4: // Web service
						$domaines[$k]['cls'] = 'primary';
					break;
					case 5: // Telecom
						$domaines[$k]['cls'] = 'purple';
					break;
				}
			}
		}

		return $domaines;
	}

	/**
	 * Affecte/Désaffecte un domaine a une société
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param  array $get USELESS
	 * @param  array $post $_POST
	 */
	public function _setDomaine($get,$post) {
		$return = true;

		if (!$post['id_societe']) throw new errorATF("ID_SOCIETE_MISSING",3256);
		if (!$post['domaine']) throw new errorATF("DOMAINE_MISSING",3257);

		if ($post['id']) {
			// Delete la liaison
			ATF::societe_domaine()->delete($post['id']);
		} else {
			// Ajout de la liaison
			$return = ATF::societe_domaine()->insert(array("id_societe"=>$post['idSociete'], 'id_domaine'=>$post['domaine']));
		}

		return $return;
	}


	/**
	* Permet de récupérer la liste des societes pour la geolocalisation
	* @package Telescope
	* @author Charlier Cyril <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/

	public function _getGeoloc($get,$post) {
		$colsData = array(
			"id_societe"=>array(),
			"societe"=>array(),
			"longitude"=>array(),
			"latitude"=>array(),
			"ville"=>array(),
			"cp"=>array(),
			"adresse"=>array()
		);
		$this->q->reset();
		$this->q->addField($colsData)
				->setCount()
				->whereIsNotNull("longitude")
				->addConditionNotNull("latitude");

		$return = $this->select_all();
		header("ts-total-row: ".$return['count']);
		return $return;
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion société
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_societe")->addField("societe")->addField("ref")->addField("nom_commercial");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		// Clause globale
		$this->q->where("etat","actif");

		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion société
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function _acFournisseur($get,$post) {

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_societe")->addField("societe")->addField("ref")->addField("nom_commercial");



		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		// Clause globale
		$this->q->where("etat","actif")
				->where("fournisseur","oui")
				->addOrder("societe","asc");


		return $this->select_all();
	}


}
