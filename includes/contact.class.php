<?
/**  
* Classe contact
* Cet objet permet de gérer les entités au sein du CRM
* @package Optima
*/
class contact extends classes_optima {
	/** 
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'contact.prenom'
			,'contact.nom'
			,'contact.id_societe'
			,'contact.fonction'
			,'contact.tel' => array("tel"=>true,"renderer"=>"tel","width"=>120)
			,'contact.gsm' => array("tel"=>true,"renderer"=>"tel","width"=>120)
			,'contact.email' => array("renderer"=>"email","width"=>250)
			,'completer' => array("custom"=>true,"renderer"=>"progress","aggregate"=>array("min","avg"),"width"=>100)
			,'actions'=>array("custom"=>true,"nosort"=>true,"renderer"=>"actionsContact","width"=>60)
		);
		
		$this->colonnes['primary'] = array(
			"id_societe"=> array("custom"=>true)
			,"id_owner"=> array("custom"=>true,"null"=>true)
			,"nom_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"civilite"=>array("width"=>90)
				,"prenom"
				,"nom"
			))
			,"fonction"
		);
		
		// Adresse
		$this->colonnes['panel']['adresse_complete_fs'] = array(
			"adresse"
			,"adresse_2"
			,"adresse_3"
			,"cp_ville"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"cp"
				,"ville"
			))
			,"id_pays"
			,"tel_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"tel"=>array("quick_update"=>true,"custom"=>true,"tel"=>true)
				,"gsm"=>array("custom"=>true,"tel"=>true)
				,"fax"
			))
			,"email"=>array("quick_update"=>true)
		);
		$this->panels['adresse_complete_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);

		// Disponibilité
		$this->colonnes['panel']['dispo_fs'] = array(
			"disponibilite"/*=>array("xtype"=>"checkboxgroup")*/
			,"autres"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"tel_autres"=>array("quick_update"=>true,"tel"=>true)
				,"adresse_autres"
			))
			,"assistants"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"assistant"
				,"assistant_tel"=>array("tel"=>true)
			))
		);
		$this->panels['dispo_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);

		// Blocs Adresses
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse_complete"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'adresse_complete_fs')
			,"dispo"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'dispo_fs')
		);
		$this->panels['coordonnees'] = array("visible"=>true);


		/* mis en commentaire pour le select_all extjs
		$this->colonnes['bloquees']['select'] = array(	
			"adresse_2"
			,"adresse_3"
			,"ville"
			,"cp"
			,"id_pays"
		);*/
		$this->colonnes['bloquees']['insert'] = 
		$this->colonnes['bloquees']['update'] = array(	
			"date"	
		);

		$this->autocomplete = array(
			"field"=>array("contact.civilite","contact.prenom","contact.nom")
			,"show"=>array("contact.civilite","contact.prenom","contact.nom")
			,"popup"=>array("contact.nom","contact.prenom","contact.societe")
			,"view"=>array("contact.id_contact","societe.societe","contact.tel","contact.gsm")
		);

		$this->colonnes["speed_insert"] = array(
			'civilite'	
			,'prenom'
			,'nom'
			,'id_societe'
			,'tel'
			,'email'
		);
		
		$this->fieldstructure();
		
		$this->field_nom = "%civilite% %prenom% %nom%";
		$this->onglets = array('suivi'=>array('table'=>'suivi_contact','opened'=>true,'field'=>'suivi_contact.id_contact'),'devis');
		$this->addPrivilege("getMail");
		$this->addPrivilege("export_vcard","export");
		$this->addPrivilege("autocompleteAvecMail");
		$this->addPrivilege("export_vcard","select");
		
		$this->no_update_all = false; // Pouvoir modifier massivement
		
		$this->syncLdap = true; // Synchronisation avec ldap, cet attribut permet de pouvoir l'empecher dans d'autres projets)
		
		// Expérimental
//		$this->selectAllExtjs=true;
	}
	
	// Méthode de nico
	public function export_vcard($infos){
		$contact=ATF::contact()->select($infos['id']);

		$vcard = $this->createVcard($contact['id_contact']);
		
		// recherche des informations concernant le contact et sa société
		header("Content-Disposition: attachment; filename=".str_replace(" ","",$contact['nom'])."_".str_replace(" ","",$contact['prenom']).".vcf");
		$fh=fopen($vcard, "rb");
		fpassthru($fh);
		unlink($vcard);
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
		$this->q->addField("(IF(LENGTH(contact.prenom)>0,1,0)".
			"+IF(LENGTH(contact.nom)>0,1,0)".
			"+IF(LENGTH(contact.email)>0,1,0)".
			"+IF(LENGTH(contact.tel)>0 || LENGTH(contact.gsm)>0,1,0)".
			"+IF(LENGTH(contact.fonction)>0,1,0)".
			"+IF(LENGTH(contact.anniversaire)>0,1,0))*100/6","completer")
			->addField("contact.etat");
		$return = parent::select_all();
		foreach ($return['data'] as $k=>$i) {
			if (ATF::$usr->privilege('suivi','insert')) {
				$return['data'][$k]['allowSuivi'] = true;
			}
		}
		return $return;
	}
	
	/**
	* Autocomplete avec les termes associés à chaque société
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteAvecMail($infos,$reset=true) {
		
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->addField("contact.email")
			->addCondition("contact.etat","actif");
		$return = $this->autocomplete($infos,false);
		foreach ($return as $key => $value) {
			$civilite = $this->select($value[1] , "civilite");
			$return[$key]["civilite"] = $civilite;
		}
		return $return;
		
	}
	
	/**
	* Autocomplete sur les contacts qui ont leur état actif
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocomplete($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q			
			->where("contact.etat","actif");
		return parent::autocomplete($infos,false);
	}
		
	/** 
	* Donne le mail du contact ainsi qu'un texte formatté pour l'utilisation sur le module devis (devis-update_field.tpl.htm)
	* @param array $infos et notamment le paramètre $infos["id_contact"]
	* @return array $infos ($infos['email'] et $infos['text']
	*/
	public function getMail($infos,&$s,$files=NULL,&$cadre_refreshed,$nolog=false){
		$infos["email"]=$this->select($infos["id_contact"],"email");
		$infos["text"]="Bonjour,\nDevis effectué le ".date("d-m-y").".\n\nCordialement ".ATF::user()->nom(ATF::$usr->getID()).".";
		ATF::$cr->rm('top');
		return $infos;
	}
	
	/** 
	* Surcharge de la méthode insert
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param array $infos Les informations à insérer
	* @param array $s la session
	* @param array $files Les fichiers uploadés éventuels
	* @param array $cadre_refreshed Le cadre refreshed utilisé pour le rafraichissement ajax
	*/
	public function insert(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		if(is_array($infos['contact'])){
			$infos=$infos['contact'];
		}	
		
		$infos['nom']=strtoupper($infos['nom']);
		$infos['prenom']=ucfirst($infos['prenom']);
				
		if (!$infos['id_owner'] && is_array($s) && isset(ATF::$usr)) {
			$infos['id_owner'] = ATF::$usr->getID();
		}
		
		$return = parent::insert($infos,$s,$files,$cadre_refreshed);
		
		if ($this->syncLdap) {
			// Insertion d'un contact dans Ldap le s'il est activé
			ATF::ldap(__FUNCTION__,$this->parseLdap($infos));		
		}
		
		return $return;
	}
	
	/** 
	* Mise à jour de contact
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Les informations à insérer
	* @param array $s la session
	* @param array $files Les fichiers uploadés éventuels
	* @param array $cadre_refreshed Le cadre refreshed utilisé pour le rafraichissement ajax
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		// Sauvegarde de l'ancien CN ldap
		$this->infoCollapse($infos);
		
		ATF::db($this->db)->begin_transaction();
		
		// Suppression de l'ancien contact dans Ldap le s'il est activé
		if ($this->syncLdap) {
			ATF::ldap("delete",$this->parseLdap($this->select($infos["id_".$this->table])));	
		}
			
		$return = parent::update($infos,$s,$files,$cadre_refreshed);
	
		// Ajout du contact modifié dans Ldap le s'il est activé
		if ($this->syncLdap) {
			ATF::ldap("insert",$this->parseLdap($infos));	
		}
		
		ATF::db($this->db)->commit_transaction();
		
		return $return;
	}
	
//	/** 
//	* Suppression de contact
//	* @author Yann GAUTHERON <ygautheron@absystech.fr>
//	* @param array $infos Les informations à insérer
//	* @param array $s la session
//	* @param array $files Les fichiers uploadés éventuels
//	* @param array $cadre_refreshed Le cadre refreshed utilisé pour le rafraichissement ajax
//	*/
//	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
//		$return = parent::delete($infos,$s,$files,$cadre_refreshed);
//		
//		if ($this->syncLdap) {
//			if (is_numeric($infos)) { // Si on a directement un ID passé en paramètre
//				// Suppression d'un contact dans Ldap le s'il est activé
//				ATF::ldap(__FUNCTION__,$this->parseLdap($this->select($infos)));		
//			}
//		}
//		
//		return $return;
//	}

	/** 
	* Impossible de supprimer un contact car cela peut entraîner une suppression en cascade dangereuse
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean 
	*/
	public function can_delete($id){
		throw new error("Impossible de supprimer un contact, il faut le passer en inactif",879);
		return false;
	}
	
	/** 
	* Grise les contacts qui sont inactifs
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array donnees : les donnees de la ligne du select_all (donc qui se base sur les colonnes)
	* @return string class css à appliquer
	*/
	public function applique_css(&$donnees){
		if($etat=$donnees['contact.etat']){
			if($etat=="inactif")return 'grise';
		}else{
			$etat=$this->select($donnees['contact.id_contact'],'etat');
			if($etat=="inactif")return 'grise';
		}
		return NULL;		
	}
	
	/** 
	* Transformation
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Les informations à insérer
	* @return array Informations structurées pour Ldap
	*/
	public function parseLdap($infos){
		$info["objectclass"][] = "inetOrgPerson";
		$info["objectclass"][] = "organizationalPerson";
		$info["objectclass"][] = "top";
		$info["cn"] = $infos["nom"];
		if ($infos["prenom"]) {
			$info["cn"] = $infos["prenom"]." ".$info["cn"];
		}
		if ($infos["id_societe"]) {
			$info["cn"] .= ", ".ATF::societe()->nom($infos["id_societe"]);
		}
		$info["sn"] = $infos["nom"];
		$info["mail"] = $infos["email"];
		$info["street"] = $infos["adresse"]." ".$infos["adresse_2"]." ".$infos["adresse_3"];
		$info["o"] = ATF::societe()->nom($infos["id_societe"]);
		$info["l"] = $infos["ville"];
		$info["postalCode"] = $infos["cp"];
		if ($infos["tel"]) {
			$info["telephoneNumber"][] = $infos["tel"];
		}
		if ($infos["gsm"]) {
			$info["telephoneNumber"][] = $infos["gsm"];
		}
		$info["facsimileTelephoneNumber"] = $infos["fax"];
		
		// Supprimer les valeurs vides
		foreach ($info as $k => $i) {
			if (!$i) {
				unset($info[$k]);
			}
		}
		
		return $info;
	}
	
	/** 
	* Créer le QRCode d'une vcard
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id ID du contact
	* @return string URL vers la vignette 150px du QRcode
	*/
	public function vcardToQRcode($id,$temp=true,$rev=true) {
		$vcard = self::createVcard($id,$temp,$rev);
		util::QRcode($this->filepath($id,"qrcode"),file_get_contents($vcard));

		unlink($vcard);
		return $this->table."-".$this->cryptId($id)."-qrcode-150.png?v=".util::generateRandWord();
	}

	/** 
	* Génère la vcard d'un contact
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id ID du contact
	* @return bool Indicateur de ficheir temporaire ou non.
	*/
	public function createVcard($id,$temp=true,$rev=true) {
		$contact = $this->select($id);
		$societe = ATF::societe()->select($contact['id_societe']);

		$target = $this->filepath($id,"vcard",$temp);
		if (!util::mkdir(dirname($target))) throw new error("Le dossier <i>".$target."</i> ne s'est pas créer ");

		if(file_exists($target)){
			unlink($target);
		}
		touch($target);
		if($fichier=fopen($target,"w")){
//			$address = $contact['adresse'].($contact['adresse_2']?";".$contact['adresse_2']:"").($contact['adresse_3']?";".$contact['adresse_3']:"").($contact['cp']?";".$contact['cp']:"").($contact['ville']?";".$contact['ville']:"");
//			if (!$address) {
//			}
			$address = $societe['adresse'].($societe['adresse_2']?";".$societe['adresse_2']:"").($societe['adresse_3']?";".$societe['adresse_3']:"").";".$societe["cp"].";".$societe["ville"];

			$d = array(
				"nom"=>$contact['nom'].";".$contact['prenom'],
				"fNom"=>$contact['nom']." ".$contact['prenom'],
				"societe"=>$societe['societe'],
//				"fonction"=>$contact['fonction'],
				"tel"=>$contact['tel'],
				"cell"=>$contact['gsm'],
				"address"=>$address,
				"email"=>$contact['email'],
			);
			if ($rev) $d['rev']=date("YmdHis");

			ATF::$html->assign("d",$d);

			fwrite($fichier,ATF::$html->fetch("vcard.tpl.htm"));
			fclose($fichier);
		}

		return $target;
	}

	/**
    * Retourne les contact de la societe
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param string $id_societe	
	* @return array
    */   
	public function getContactFromSociete($id_societe){
		$id_societe = ATF::societe()->decryptId($id_societe);
		$this->q->reset()->where("contact.id_societe",$id_societe)->where("contact.etat","actif");
		return parent::autocomplete(true,false);
	}
	

/* PARTIE DES FONCTIONS POUR TELESCOPE*/


	/** Fonction qui génère les résultat pour les champs d'auto complétion contact
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function _ac($get,$post) {
		$length = 25;
		$start = 0;

		$this->q->reset();

		// On ajoute les champs utiles pour l'autocomplete
		$this->q->addField("id_contact")->addField("nom")->addField("prenom")->addField("fonction");

		if ($get['q']) {
			$this->q->setSearch($get["q"]);
		}

		if ($get['id_societe']) {
			$this->q->where("id_societe",$get["id_societe"]);
		}

		// Clause globale
		$this->q->where("etat","actif");

		$this->q->setLimit($length,$start)->setPage($start/$length);

		return $this->select_all();
	}

};
?>