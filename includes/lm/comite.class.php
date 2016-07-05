<?
/** Classe comite
* @package Optima
* @subpackage Cléodis
*/
class comite extends classes_optima {
	function __construct($table_or_id) {
		$this->table ="comite";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'comite.date'
			,'comite.id_affaire'
			,'comite.id_societe'
			,'comite.etat'=>array("width"=>30,"renderer"=>"etat")
			,'comite.validite_accord'=>array("renderer"=>"updateDate","width"=>170)					
			,'decision'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"comiteDecision","width"=>50)
			,"decisionComite"
		);
		
		$this->colonnes['primary'] = array(		 
			"id_societe"=>array("disabled"=>true)
			,"id_affaire"=>array("disabled"=>true)		
			,"date_creation"	

		);		

		$this->colonnes['panel']['dates'] = array(
			"date"
			,"reponse"
			,"validite_accord"
		);

		$this->colonnes['panel']['notes'] = array(
			"description"
			,"observations"
		);

		$this->colonnes['panel']['notifie'] = array(
			"suivi_notifie"=>array("custom"=>true)
		);
		
		$this->field_nom = "description";

		$this->colonnes['bloquees']['select'] =   array_merge(array('note'));

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['update'] =
		$this->colonnes['bloquees']['clone']  = array_merge(array('etat','commentaire','decision', 'decisionComite', "notifie_utilisateur"));

		$this->fieldstructure();

		//$this->colonnes['bloquees']['insert'] = array('score',"avis_credit");	

		$this->noTruncateSA = true;
		$this->panels['loyer_lignes'] = array('nbCols'=>1);
		$this->panels['chiffres'] = array("visible"=>true, 'nbCols'=>4);
		$this->panels['dates'] = array("visible"=>true, 'nbCols'=>3);
		$this->panels['notes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['statut'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['creditSafeInfos'] = array("visible"=>true, 'nbCols'=>4);
		$this->no_insert = true;
		$this->selectAllExtjs=true; 

		$this->addPrivilege("getInfosFromCREDITSAFE");
		$this->addPrivilege("decision");

	}

	/** 
	* Surcharge de l'insert afin de modifier l'etat de l'affaire
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false,$tu=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}		

		log::logger($infos , "mfleurquin");

		$this->infoCollapse($infos);

		$notifie_suivi = $infos["suivi_notifie"];
		$notifie_suivi = array_unique($notifie_suivi);
		unset($infos["suivi_notifie"], $infos["id_devis"]);




//*****************************Transaction********************************
		ATF::db($this->db)->begin_transaction();
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
		

					
		if($notifie_suivi != array(0=>"")){
			$recipient = "";
			$info_mail["suivi_notifie"] = "";

			log::logger($notifie_suivi , "mfleurquin");

			foreach ($notifie_suivi as $key => $value) {
				log::logger($value , "mfleurquin");
				$info_mail["suivi_notifie"] .= ATF::user()->nom($value).",";
				$recipient .= ATF::user()->select($value,"email").",";
			}

			$recipient = substr($recipient, 0, -1);
			$info_mail["suivi_notifie"] = substr($info_mail["suivi_notifie"], 0, -1);

			$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".ATF::user()->select(ATF::$usr->getID(),"email").">";;
			$info_mail["html"] = true;
			$info_mail["template"] = "comite";
			
			$info_mail["recipient"] = $recipient;
			$info_mail["objet"] = "Une demande de comité vient d'être créée pour l'affaire ".ATF::affaire()->select($infos['id_affaire'], "ref").".";
			
			$info_mail["reception_comite"] = "Un comité est en attente de votre part, voici quelques informations concernant ce dernier";
			$info_mail["id_user"] = ATF::$usr->getID();
			$info_mail["id_societe"] = $infos["id_societe"];
			$info_mail["id_affaire"] = $infos["id_affaire"];
			$info_mail["optima_url"] = ATF::permalink()->getURL($this->createPermalink($this->cryptId($last_id)));
			
			

			$mail = new mail($info_mail);
		
			if(!$tu) $mail->send();						

			$this->u(array("id_comite"=>$last_id , "notifie_utilisateur"=>$info_mail["suivi_notifie"]));
		}

			
			ATF::db($this->db)->commit_transaction();
		

		if(is_array($cadre_refreshed)){	ATF::affaire()->redirection("select",$infos["id_affaire"]);	}
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
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false,$tu=false){

		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		
		$this->infoCollapse($infos);

		
		foreach ($infos["suivi_notifie"] as $key => $value) {
			$user_notifie .= ATF::user()->nom($value).",";			
		}
		$user_notifie = substr($user_notifie, 0, -1);

		$infos["notifie_utilisateur"] = $user_notifie;


		$infos["filestoattach"]["pdf"]="";
		$notifie_suivi = $infos["suivi_notifie"];	

		ATF::devis()->q->reset()->where("devis.id_affaire", $this->select($id, "id_affaire"));
		$devis = ATF::devis()->select_row();

		$notifie_suivi[] = 35;
		$notifie_suivi[] = $devis["id_user"];
		$notifie_suivi[] = ATF::$usr->getID();

		$notifie_suivi = array_unique($notifie_suivi);


		unset($infos["suivi_notifie"]);
		/*****************************Transaction********************************/
		ATF::db($this->db)->begin_transaction();
		parent::update($infos,$s,$files);
		
		if($preview){
			if(!$tu) $this->move_files($infos["id_comite"],$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($infos["id_comite"]);
		}else{
			if(!$tu) $this->move_files($infos["id_comite"],$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base

			
			if($infos["etat"]){
				$suivi = array(
					 "id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$infos['id_societe']
					,"id_affaire"=>$infos['id_affaire']
					,"type_suivi"=>'Passage_comite'
					,"texte"=>"La demande de comité vient de changer d'état (nouvel état : ".ATF::$usr->trans($infos["etat"],$this->table).")\n par ".ATF::$usr->getNom().".\n Refinanceur : ".ATF::refinanceur()->select($infos["id_refinanceur"] , "refinanceur")
					,'public'=>'oui'					
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
					,'suivi_notifie'=>$notifie_suivi
					,"permalink"=> ATF::permalink()->getURL($this->createPermalink($infos["id_comite"]))
					,'no_redirect'=>true
				);
				if(!$tu) $id_suivi = ATF::suivi()->insert($suivi);
			}		

			ATF::db($this->db)->commit_transaction();
			/****************************************************************************/

			if(is_array($cadre_refreshed)){	ATF::affaire()->redirection("select",$infos["id_affaire"]);	}
			
			$id_comite=$this->decryptId($infos["id_comite"]);
			return $id_comite;
		}

	}

	/** 
	* Impossible de modifier un devis qui n'est pas en attente
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id
	* @return boolean 
	*/
	public function can_update($id,$infos=false){
		if($this->select($id,"etat")=="en_attente"){
			return true;
		}else{
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il n'est plus en '".ATF::$usr->trans("attente")."'",892);
			return false; 
		}
	}


	/**
	* Permet de modifier la date sur un select_all
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function updateDate($infos){

		if ($infos['value'] == "undefined") $infos["value"] = "";

		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];

		if(array_key_exists("validite_accord",$infosMaj)){
			$data=array("id_comite"=>$infosMaj["id_".$this->table], "validite_accord"=>$infos["value"]);
		}elseif(array_key_exists("date_cession",$infosMaj)){
			$data=array("id_comite"=>$infosMaj["id_".$this->table], "date_cession"=>$infos["value"]);
		}
		if($this->u($data)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);
		}else{ return false; }
		return true;
	}	
	
	/**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field,&$s,&$request){
			$id_devis = ATF::_r("id_devis");

			$id_affaire=ATF::devis()->select(ATF::devis()->decryptId($id_devis), "id_affaire");
			$affaire = ATF::affaire()->select($id_affaire);
			$societe = $affaire["id_societe"];
			
		switch ($field) {
			case "id_affaire":	return $id_affaire; 
			case "id_societe": return $affaire['id_societe'];
			case "id_user":	return ATF::$usr->get('id_user');
			case "date":
				return date("d-m-Y");			
			case "prix":				
					$devis = ATF::affaire()->getDevis($affaire['id_affaire']);
					return $devis->infos['prix'];	
			case "description":	return $affaire['affaire'];
			case "loyer_actualise":		return ATF::affaire()->getCompteTLoyerActualise($affaire);
		}
	
		return parent::default_value($field,$s,$request);
	}


	public function decision($infos){		

		$validite = explode("/", $infos["date"]);
		$validite_accord = $validite[2]."-".$validite[1]."-".$validite[0];
		
		$id = $this->decryptId($infos["id"]);

		if($infos["comboDisplay"] == "refus_comite"){
			$etat = "refuse";		
		}else{
			$etat = "accepte";	
		}

		$data = array("id_comite"=>$id,
					  "etat"=>$etat,
					  "commentaire"=>$infos["commentaire"],
					  "reponse"=>date("Y-m-d"),
					  "decisionComite"=>$infos["decision"]
					);
		$this->u($data);

		ATF::devis()->q->reset()->where("devis.id_affaire", $this->select($id, "id_affaire"));
		$devis = ATF::devis()->select_row();


		//Envoi du mail au prestataire & passage du contrat en en_attente
		ATF::commande()->q->reset()->where("commande.id_affaire",$this->select($id, "id_affaire"));
		$commande = ATF::commande()->select_row();

		if($commande){
			ATF::commande()->u(array("id_commande"=>$commande["commande.id_commande"], "etat"=>"non_loyer"));
			self::envoiNotificationPrestataires($commande["commande.id_commande"]);
		}	
	}

	/** 
    * 
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @param string $prefixe Le préfixe de la référence (exemple SLI0911)
    * @return int la référence max
    */
    public static function envoiNotificationPrestataires($id_commande){
        // Pour chaque prestataire concerné, on envoi un email de commande
        
        try {
            ATF::commande()->q->reset()
                ->select('societe.email_notification')
                //->select('societe.societe')
                ->select('commande_ligne.id_fournisseur')
                ->from("commande","id_commande","commande_ligne","id_commande")
                ->from("commande_ligne","id_fournisseur","societe","id_societe")
                ->where("commande.id_commande", $id_commande)
                ->setStrict()
                ->addGroup('commande_ligne.id_fournisseur');
            if ($fournisseurs = ATF::commande()->select_all()) {            	
                foreach ($fournisseurs as $f) {
                	$f = ATF::societe()->select($f["commande_ligne.id_fournisseur"]);                	
                    if ($f["email_notification"]) {
                        $commande = self::getInfosCommande($id_commande,$f["id_societe"]);
                        
                        $info_mail["from"] = __EMAIL_FROM_NAME__." <".__EMAIL_FROM__.">";;
												
						$info_mail["recipient"] = $f["email_notification"];
						$info_mail["objet"] = "[LMA] Nouvelle souscription de ".$commande["prenom"]." ".$commande["nom"]." du pack ";												
						$info_mail["body"] = json_encode($commande, JSON_PRETTY_PRINT);
					
						$mail = new mail($info_mail);
					
						$mail->send();	                        
                    }
                }
            }
        } catch (Exception $e) {
            log::logger($e->getMessage(),"mail_error");
        }
    }   


    /**
    * Permet de recuperer une commande
    * @author Anthony Lahlah <alahlah@absystech.fr>
    * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
    * @param get et post classique
    * @return array
    */
    public static function getInfosCommande($id_commande, $id_fournisseur){ // fonction get accessible uniquement par le prestataire
        $q = "SELECT 
                commande.id_commande, 
                commande.ref, 
                commande.commande, 
                commande.id_societe, 
                societe.prenom,
                societe.nom,
                societe.tel,
                societe.email,
                affaire.adresse_livraison, 
                affaire.cp_adresse_livraison, 
                affaire.ville_adresse_livraison 
            FROM commande 
                INNER JOIN commande_ligne ON commande.id_commande = commande_ligne.id_commande 
                INNER JOIN affaire ON affaire.id_affaire=commande.id_affaire 
                INNER JOIN societe ON societe.id_societe=commande.id_societe 
            WHERE 
                commande.id_commande = '".ATF::db()->real_escape_string($id_commande)."' 
            GROUP BY commande.id_commande";
        $cmd = ATF::db()->fasso($q);
        $q = "SELECT * FROM commande_ligne WHERE id_fournisseur='".ATF::db()->real_escape_string($id_fournisseur)."' AND id_commande='".ATF::db()->real_escape_string($id_commande)."'";
        if ($all_lines = ATF::db()->arr($q)) { // on recupère toutes ses lignes
            foreach ($all_lines as $key_lines => $lc) { // pour chaque lignes de la commande
                $q = "SELECT produit, id_produit, ref_lm FROM produit WHERE id_produit='".ATF::db()->real_escape_string($lc['id_produit'])."'";
                $p = ATF::db()->fasso($q);
                $p["quantite"] = $lc["quantite"];
                $cmd['produit'][] = $p; // on ajout dans la commande sur laquelle on itère le produit en question dans un tableau 'produit'
            }
        }
        return $cmd;
    } 
};

?>